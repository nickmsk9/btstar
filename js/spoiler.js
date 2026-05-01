function showspoiler(id){
    var text = document.getElementById(id);
    var pic = document.getElementById('pic' + id);
    if(text.style.display == 'none')
    {
        text.style.display = 'block';
        pic.src = 'pic/minus.gif';
        pic.title = 'Скрыть';
    }
    else
    {
        text.style.display = 'none';
        pic.src = 'pic/plus.gif';
        pic.title = 'Показать';
    }
}  