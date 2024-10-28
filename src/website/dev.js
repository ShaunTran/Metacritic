const file = document.getElementsByName('txt')[0].content
fetch(`../../txtfiles/${file}.txt`)
    .then((res) => res.text())
    .then((text) => {
        var publishers = text.split("\r\n");
        const result = document.querySelector(".result-box");

const input = document.getElementById("input-box");
input.onkeyup = function(){
    let out = [];
    let inp = input.value;
    if (inp.length) {
        out = publishers.filter((keyword)=>{
            return keyword.toLowerCase().includes(inp.toLowerCase());
        });
        console.log(out); 
    }
    display(out);

    if (!out.length) {
        result.innerHTML = '';
    }
}
function display(out) {
    const content = out.map((list)=>{
        return "<li onclick=selectInput(this)>" + list + "</li>";
    });
    result.innerHTML = "<ul>" + content.join('') + "</ul>";
}
window.selectInput = function(list) {
    input.value = list.innerHTML;
    result.innerHTML = '';
}
    });
