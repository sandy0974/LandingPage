document.getElementById("year").textContent = new Date().getFullYear();

document.querySelector(".menu").addEventListener("click", () => {
  const nav = document.querySelector(".nav nav");
  const open = nav.style.display === "flex";
  nav.style.display = open ? "" : "flex";
  if (!open) {
    nav.style.position = "absolute";
    nav.style.top = "78px";
    nav.style.left = "0";
    nav.style.right = "0";
    nav.style.padding = "20px 6vw";
    nav.style.flexDirection = "column";
    nav.style.background = "#08090b";
    nav.style.borderBottom = "1px solid #252830";
  }
});
