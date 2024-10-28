/*function siteRedirect() {
  var select1 = document.querySelector("scores");
  var category = select1.options[select1.selectedIndex].value,
      redirect = category;
  console.log("Redirect to: "+redirect);
  }*/
/*<script type="text/javascript">
    window.onload = function(){
        location.href= "scores=" + document.getElementById("selectbox").value
        }       
</script>*/
/*var slider = document.getElementById("fromSlider");
var mini = document.getElementById("demo");

var slider2 = document.getElementById("toSlider");
var max = document.getElementById("demo2");

var slider3 = document.getElementById("review");
var review = document.getElementById("minimum");

mini.innerHTML = slider.value; // Display the default slider value
max.innerHTML = slider2.value;
review.innerHTML = slider3.value;

// Update the current slider value (each time you drag the slider handle)
slider.oninput = function() {
  mini.innerHTML = this.value;
}
slider2.oninput = function() {
  if (this.value > slider.value) {
    max.innerHTML = this.value;
  } else {
    max.innerHTML = slider.value;
  }
}
slider3.oninput = function() {
    review.innerHTML = this.value; 
}*/
var slider = document.getElementById("fromSlider");
var mini = document.getElementById("demo");
mini.innerHTML = slider.value;

var slider2 = document.getElementById("toSlider");
var max = document.getElementById("demo2");
max.innerHTML = slider2.value;

var slider3 = document.getElementById("review");
var review = document.getElementById("minimum");
review.innerHTML = slider3.value;

function controlFromSlider(fromSlider, toSlider) {
  const [from, to] = getParsed(fromSlider, toSlider);
  
  fillSlider(fromSlider, toSlider, '#C6C6C6', '#D9BC95', toSlider);
  if (from >= to) {
    fromSlider.value = to-0;
  } 
  mini.innerHTML = fromSlider.value;
}

function controlToSlider(fromSlider, toSlider) {
  const [from, to] = getParsed(fromSlider, toSlider);
  
  fillSlider(fromSlider, toSlider, '#C6C6C6', '#D9BC95', toSlider);
  setToggleAccessible(toSlider);
  if (from < to) {
    toSlider.value = to;
  } else {
    toSlider.value = from+0;
  }
  max.innerHTML = toSlider.value;
}

function getParsed(currentFrom, currentTo) {
  const from = parseInt(currentFrom.value, 10);
  const to = parseInt(currentTo.value, 10);
  return [from, to];
}

function fillSlider(from, to, sliderColor, rangeColor, controlSlider) {
  const rangeDistance = to.max - to.min;
  const fromPosition = from.value - to.min;
  const toPosition = to.value - to.min;
  controlSlider.style.background = `linear-gradient(
      to right,
      ${sliderColor} 0%,
      ${sliderColor} ${(fromPosition)/(rangeDistance)*100}%,
      ${rangeColor} ${((fromPosition)/(rangeDistance))*100}%,
      ${rangeColor} ${(toPosition)/(rangeDistance)*100}%, 
      ${sliderColor} ${(toPosition)/(rangeDistance)*100}%, 
      ${sliderColor} 100%)`;
}

function setToggleAccessible(currentTarget) {
  const toSlider = document.querySelector('#toSlider');
  if (Number(currentTarget.value) <= 0) {
    toSlider.style.zIndex = 2;
  } else {
    toSlider.style.zIndex = 0;
  }
}

const fromSlider = document.querySelector('#fromSlider');
const toSlider = document.querySelector('#toSlider');
fillSlider(fromSlider, toSlider, '#C6C6C6', '#D9BC95', toSlider);
setToggleAccessible(toSlider);

fromSlider.oninput = () => controlFromSlider(fromSlider, toSlider);
toSlider.oninput = () => controlToSlider(fromSlider, toSlider);
slider3.oninput = function() {
  review.innerHTML = this.value; 
}