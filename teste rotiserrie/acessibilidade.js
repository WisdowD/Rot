// ================== ABRIR / FECHAR MODAL ==================
document.getElementById("btn-accessibility").onclick = () => {
    document.getElementById("accessibility-modal").style.display = "flex";
};

document.getElementById("acc-close").onclick = () => {
    document.getElementById("accessibility-modal").style.display = "none";
};


// ================== TAMANHO DA FONTE ==================
let currentFont = parseFloat(localStorage.getItem("fontSize")) || 1;
document.documentElement.style.fontSize = currentFont + "em";

document.getElementById("acc-font-plus").onclick = () => {
    currentFont += 0.1;
    document.documentElement.style.fontSize = currentFont + "em";
    localStorage.setItem("fontSize", currentFont);
};

document.getElementById("acc-font-minus").onclick = () => {
    currentFont -= 0.1;
    if (currentFont < 0.7) currentFont = 0.7;
    document.documentElement.style.fontSize = currentFont + "em";
    localStorage.setItem("fontSize", currentFont);
};


// ================== DARK MODE ==================
document.getElementById("acc-darkmode").onclick = () => {
    document.documentElement.classList.toggle("dark");
    toast("Modo escuro alternado");
};


// ================== ALTO CONTRASTE ==================
document.getElementById("acc-contrast").onclick = () => {
    document.body.classList.toggle("high-contrast");
    toast("Alto contraste ativado");
};


// ================== DESATIVAR ANIMAÇÕES ==================
document.getElementById("acc-no-animation").onclick = () => {
    document.body.classList.toggle("no-animation");
    toast("Animações desativadas");
};


// ================== LER ITEM (BOTÃO DIREITO) ==================
function lerTexto(texto) {
    window.speechSynthesis.cancel();
    const fala = new SpeechSynthesisUtterance(texto);
    fala.lang = "pt-BR";
    window.speechSynthesis.speak(fala);
}

document.querySelectorAll(".menu-card").forEach(card => {
    card.addEventListener("contextmenu", e => {
        e.preventDefault();
        let nome = card.querySelector("h3")?.innerText || "";
        let preco = card.querySelector(".card-price")?.innerText || "";
        lerTexto(nome + ". " + preco);
    });
});


// ================== RESET ==================
document.getElementById("acc-reset").onclick = () => {
    localStorage.clear();
    document.documentElement.classList.remove("dark");
    document.body.classList.remove("high-contrast");
    document.body.classList.remove("no-animation");
    document.documentElement.style.fontSize = "1em";
    toast("Acessibilidade restaurada");
};


// ================== TOAST ==================
function toast(msg) {
    const el = document.getElementById("toast");
    el.textContent = msg;
    el.classList.add("show");

    setTimeout(() => {
        el.classList.remove("show");
    }, 2000);
}

function lerItem(nome, descricao) {
    try {
        speechSynthesis.cancel();

        const texto = `${nome}. ${descricao}`;
        const fala = new SpeechSynthesisUtterance(texto);
        fala.lang = "pt-BR";

        speechSynthesis.speak(fala);
    } catch (e) {
        console.error("Erro no leitor:", e);
    }
}
