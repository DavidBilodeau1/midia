document.addEventListener('DOMContentLoaded', function(){
var chapters = document.querySelectorAll('#flipbook .chapter');
chapters.forEach((chapter, i) => {
    var pages = [];
    var elements = chapter.querySelectorAll('p, h1');
    //pages = countPages(0, elements, pages);
    console.log(pages);
});
function countPages(offset, elements, pages){
    var currentHeight = 0;
    var pageHeight = 800;
    var page = document.createElement('div');
    var i = offset;
    while (currentHeight < pageHeight && i < elements.length) {
        currentHeight += elements[i].offsetHeight;
        if((currentHeight + elements[i].offsetHeight) > pageHeight){
            pages = countPages(i , elements, pages);
        }
        else{
            appendHtml(page, elements[i].innerHTML);
        }
        i++;
    }
    pages.unshift(page);
    return pages;
}

function appendHtml(el, str) {
  var div = document.createElement('div');
  div.innerHTML = str;
  while (div.children.length > 0) {
    el.appendChild(div.children[0]);
  }
}

$("#flipbook .double").scissor();
$("#flipbook").turn({
    width: 1080,
    height: 800,
    when: {
        turning: function(event, page, pageObject){
            var pages = $('#flipbook').turn('pages');
            var progress = (page / (pages - 1) * 100);
            document.querySelector('.progress .progress-bar').style.width = progress + "%";
            document.querySelector('.progress-group .progress-group-label').innerHTML = page + " / " + (pages - 1);
        }
    }
});
$(function(){
    var pages = $('#flipbook').turn('pages');
    var page = $('#flipbook').turn('page');
    var progress = (page / (pages - 1) * 100);
    document.querySelector('.progress .progress-bar').style.width = progress + "%";
    document.querySelector('.progress-group .progress-group-label').innerHTML = page +" /" + (pages - 1);
});
$('body').on('click','.fullscreen', function(){
    document.querySelector('.fullscreen').innerHTML = `<i class="fat fa-compress"></i>`;
    document.querySelector('.fullscreen').classList.add('lowerscreen');
    document.querySelector('.fullscreen').classList.remove('fullscreen');
    $("#flipbook").turn("zoom", 2.0);
});
$('body').on('click','.lowerscreen', function(){
    document.querySelector('.lowerscreen').innerHTML = `<i class="fat fa-expand"></i>`;
    document.querySelector('.lowerscreen').classList.add('fullscreen');
    document.querySelector('.lowerscreen').classList.remove('lowerscreen');
    $("#flipbook").turn("zoom", 1.0);
});
});
