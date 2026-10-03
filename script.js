// Hide the flash message after a few seconds
document.addEventListener("DOMContentLoaded", function () {
  var f = document.getElementById("flash");
  if (f) {
    setTimeout(function () {
      f.style.transition = "opacity .4s";
      f.style.opacity = "0";
      setTimeout(function () { f.remove(); }, 400);
    }, 3500);
  }
});

function validateCheckout() {
  var name = document.getElementById("name").value.trim();
  var phone = document.getElementById("phone").value.trim();
  var address = document.getElementById("address").value.trim();
  if (name === "" || address === "") {
    alert("Please enter your name and delivery address.");
    return false;
  }
  if (!/^[0-9]{10}$/.test(phone)) {
    alert("Phone number must be exactly 10 digits.");
    return false;
  }
  return true;
}

function validateRegister() {
  var name = document.getElementById("rname").value.trim();
  var email = document.getElementById("remail").value.trim();
  var phone = document.getElementById("rphone").value.trim();
  var pass = document.getElementById("rpass").value;
  if (name === "") { alert("Please enter your name."); return false; }
  if (!/^\S+@\S+\.\S+$/.test(email)) { alert("Please enter a valid email address."); return false; }
  if (!/^[0-9]{10}$/.test(phone)) { alert("Phone number must be exactly 10 digits."); return false; }
  if (pass.length < 6) { alert("Password must be at least 6 characters."); return false; }
  return true;
}
