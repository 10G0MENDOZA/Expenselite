var script = document.querySelector('.ir');
window.onscroll=function(){
    //console.log(document.documentElement.scrollTop);
    if(document.documentElement.scrollTop > 100){
        document.querySelector('.ir').style.display='block';

    }
    else{
        document.querySelector('.ir').style.display='none';
    }

    script.addEventListener('click',function(){
        window.scrollTo({
            top:0,
            behavior: 'smooth'
        })
    })
}

const menubtn = document.querySelector('.menubtn')
    const navlinks = document.querySelector('.navlinks')

    menubtn.addEventListener('click' ,()=>{
        navlinks.classList.toggle('movil-menu')
    })