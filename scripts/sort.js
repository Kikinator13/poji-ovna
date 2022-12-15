let clicked = false;
window.onload = function () {
    //Vybereme všechny hlavičkové buňky v hlavičkovém řádku. (Ty co se zobrazují v mobilní verzi ignorujeme)
    let allTh = document.getElementById("user-head-row").children;
    //Převedeme kolekci na pole aby jsme mohli odstranit poslední sloupec s ikonami.
    allTh = Array.from(allTh);
    //Odstraníme z pole sloupec akce. 
    allTh.pop();
    //Hlavičkovým buňkám nastavíme řadící funkci order jako událost
    for (let i = 0; i < allTh.length; i++) {
        allTh[i].style.cursor = "pointer"; //Řadící hlavičky budou mít ručičkový kurzor.
        allTh[i].style.cursor = "hand"; //pro ie
        
        
        allTh[i].onmousedown = order;
    }
}
function order() {
    let rows = this.parentNode.parentNode.children;
    let columnNumber = getNumberOfColumn(this);
    rows = Array.from(rows);
    rows.shift();
    let paginator = rows.pop()
    let ordered = rows;
    //ordered.forEach((p) => document.write(p.children[columnNumber*2+1].innerText+"<br>"));
    let sortAsNumber=new Array();
    sortAsNumber=[4];
    if (clicked == columnNumber) {
        ordered = ordered.reverse();
    }
    else {
        //Pokud jsou ve sloupci čísla, budeme ho řadit jako čísla.
        if (sortAsNumber.indexOf(columnNumber) >=0){
            var sortFunction = function(a, b) {
                return a.children[columnNumber * 2 + 1].innerText-b.children[columnNumber * 2 + 1].innerText;
                
            } 
                     
        }
        else {
            var sortFunction = function(a, b) {
                return b.children[columnNumber * 2 + 1].innerText.localeCompare(a.children[columnNumber * 2 + 1].innerText);
            }
        }
        ordered = ordered.sort(sortFunction);
        clicked = columnNumber;
    }
    for (let i = 0; i < rows.length; i++) {
        rows[i].parentNode.appendChild(ordered[i]);
    }
    paginator.parentNode.appendChild(paginator);
}

function getNumberOfColumn(th) {
    let row = th.parentNode;
    rowChildren = row.children;
    let columnNumber = Array.prototype.indexOf.call(rowChildren, th);
    return columnNumber;
}