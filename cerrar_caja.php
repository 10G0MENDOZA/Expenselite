<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar Dompdf
require_once __DIR__ . '/dompdf/autoload.inc.php';

// Cargar PHPMailer
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';

use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
require_once '/home2/avanceap/Credencial_Global/config.php';

// Conexión a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

if (isset($_POST['cerrar_caja']) && $_POST['cerrar_caja'] == '1') {
    $fecha = date('Y-m-d');

    $sql_last = "SELECT * FROM caja_menor ORDER BY id DESC LIMIT 1";
    $result_last = $conn->query($sql_last);

    if ($result_last->num_rows > 0) {
        $row = $result_last->fetch_assoc();
        $id_caja = $row['id'];
        $saldo_disponible = $row['saldo_disponible'];
        $saldo_gastado = $row['saldo_gastado'];
        $saldo_actual = $row['saldo_actual'];

        if (!empty($row['fecha_cierre'])) {
            echo "<div style='color:red;'>❌ Esta caja ya fue cerrada el <strong>{$row['fecha_cierre']}</strong>.</div>";
            exit;
        }

        // Cerrar caja (sin reiniciar saldo_disponible)
        $sql_update = "UPDATE caja_menor 
                       SET saldo_actual = 0, saldo_gastado = 0, fecha_cierre = '$fecha' 
                       WHERE id = $id_caja";

        if ($conn->query($sql_update) === TRUE) {
            // Reiniciar sesión (sin tocar saldo_disponible)
            $_SESSION['saldo_actual'] = 0;
            $_SESSION['saldo_gastado'] = 0;

            // Generar PDF
            $dompdf = new Dompdf();
            $html = "
                <h2 style='color:#00909E;'>Resumen de cierre de caja</h2>
                <p><strong>Fecha:</strong> $fecha</p>
                <p><strong>Saldo disponible:</strong> $saldo_disponible</p>
                <p><strong>Saldo gastado:</strong> $saldo_gastado</p>
                <p><strong>Saldo actual:</strong> $saldo_actual</p>
            ";
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $pdf_output = $dompdf->output();
            $pdf_path = __DIR__ . "/reporte_caja_$fecha.pdf";
            file_put_contents($pdf_path, $pdf_output);

            // Enviar correo
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'reportesavancelegal@gmail.com';
                $mail->Password = 'qootyyvhzfimvkey';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('reportesavancelegal@gmail.com', 'Sistema AvanceLegal');
                $mail->addAddress('recepcion@avancelegal.com.co');

                $mail->isHTML(true);
                $mail->Subject = '=?UTF-8?B?' . base64_encode('📦 Resumen de Cierre de Caja | Avance Legal | ' . $fecha) . '?=';
                $mail->Body = 'Adjunto se encuentra el PDF con el resumen del cierre de caja.';
                $mail->addAttachment($pdf_path);

                $mail->send();
            } catch (Exception $e) {
                echo "<div style='color:red;'>❌ Error al enviar correo: {$mail->ErrorInfo}</div>";
            }

            // Mostrar mensaje de éxito
            echo '
            <div class="mensaje-exito" id="mensaje-exito">
                <h2>✅ Caja cerrada exitosamente</h2>
                <p>Se ha enviado el resumen al correo de recepción.</p>
            </div>
            <style>
                .mensaje-exito {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background-color: #e8f5e9;
                    color: #2e7d32;
                    border: 2px solid #66bb6a;
                    border-radius: 12px;
                    padding: 30px;
                    font-family: "Segoe UI", sans-serif;
                    text-align: center;
                    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                    max-width: 500px;
                    width: 90%;
                    animation: fadeIn 1s ease;
                    z-index: 9999;
                }
                .mensaje-exito h2 {
                    margin-top: 0;
                    font-size: 24px;
                }
                .mensaje-exito p {
                    margin: 10px 0 0;
                    font-size: 18px;
                }
                @keyframes fadeIn {
                    from { opacity: 0; transform: translate(-50%, -60%); }
                    to { opacity: 1; transform: translate(-50%, -50%); }
                }
            </style>
            <script>
                setTimeout(function() {
                    var mensaje = document.getElementById("mensaje-exito");
                    if (mensaje) {
                        mensaje.style.transition = "opacity 0.5s ease";
                        mensaje.style.opacity = "0";
                        setTimeout(function() {
                            mensaje.remove();
                        }, 500);
                    }
                }, 3000);
            </script>';
        } else {
            echo "<div style='color:red;'>❌ Error al cerrar la caja: " . $conn->error . "</div>";
        }
    } else {
        echo "<div style='color:red;'>❌ No se encontró ninguna caja para cerrar.</div>";
    }
} else {
    echo "<div style='color:red;'>❌ Parámetros incorrectos para cerrar la caja.</div>";
}

$conn->close();

