/* Fonction Heure */
function dsptime() {
    var heure;
    date = new Date();
    h = date.getHours();
    m = date.getMinutes();
    s = date.getSeconds();
    hh = date.getHours();
    h = h > 24 ? h % 24 : h;
    heure = (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s ;         
    document.querySelector('#time').innerHTML = heure;
    return heure;
}
setInterval(dsptime, 1000);