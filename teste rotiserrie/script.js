// CARRINHO
let cart = [];
try {
  const storedCart = localStorage.getItem('rotisserieCart');
  const parsedCart = storedCart ? JSON.parse(storedCart) : [];
  // Garante que é um Array válido
  if (Array.isArray(parsedCart)) {
    cart = parsedCart;
  }
} catch (e) {
  // Se o JSON estiver corrompido, limpa a storage para evitar erros.
  console.error("Erro ao carregar carrinho do localStorage. Limpando dados.", e);
  localStorage.removeItem('rotisserieCart');
}

const cartBtn = document.getElementById("cart-btn");
const cartModal = document.getElementById("cart-modal");
const cartItems = document.getElementById("cart-items");
const cartTotal = document.getElementById("cart-total");
const cartCount = document.getElementById("cart-count");
const closeCart = document.getElementById("close-cart");
const finalizeBtn = document.getElementById("finalize-btn");

// Variável global para armazenar os detalhes do produto atual
window.currentProduct = {};

// MODAL DE PRODUTO
async function openProductModal(id) {
  const res = await fetch(`produto.php?id_item=${id}`);
  const dados = await res.json();

  window.currentProduct = {
    id: id,
    name: dados.nomeProduto,
    price: parseFloat(dados.preco)
  };
  window.currentProductId = id;

  document.getElementById("modal-image").src = dados.imagem;
  document.getElementById("modal-name").textContent = dados.nomeProduto;
  document.getElementById("modal-description").textContent = dados.descricao;
  document.getElementById("modal-price").textContent =
    "R$ " + window.currentProduct.price.toFixed(2).replace('.', ',');

  document.getElementById("product-modal").style.display = "flex";
}

window.openProductModal = openProductModal;

document.getElementById("close-product").onclick = () => {
  document.getElementById("product-modal").style.display = "none";
};

// ADICIONAR AO CARRINHO
function addToCart(itemDetails) {
  const item = cart.find(x => x.id === itemDetails.id);
  if (item) {
    item.qty++;
  } else {
    cart.push({
      id: itemDetails.id,
      name: itemDetails.name,
      price: itemDetails.price,
      qty: 1
    });
  }

  updateCart();
}

document.getElementById("modal-add-btn").onclick = () => {
  addToCart(window.currentProduct);
  document.getElementById("product-modal").style.display = "none";
};

// ATUALIZAR CARRINHO
function updateCart() {
  let total = 0;

  cartCount.textContent = cart.reduce((t, i) => t + i.qty, 0);

  cartItems.innerHTML = cart
    .map((i) => {

      const subtotal = i.price * i.qty;
      total += subtotal;

      return `
            <div class="cart-line">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="text-align: left;">
                        <strong>${i.name}</strong> (x${i.qty})
                        <small style="display:block; color:#777;">R$ ${i.price.toFixed(2).replace('.', ',')} un.</small>
                    </div>
                    <div style="font-weight: bold; min-width: 80px; text-align: right;">
                        R$ ${subtotal.toFixed(2).replace('.', ',')}
                    </div>
                    <button onclick="removeItem(${i.id})" class="remove-btn" style="background: none; border: none; cursor: pointer; font-size: 18px; padding: 0 0 0 10px;">❌</button>
                </div>
            </div>
        `;
    })
    .join("");


  cartTotal.innerHTML = `Total: <span style="color:#ff6b35;">R$ ${total.toFixed(2).replace('.', ',')}</span>`;



  localStorage.setItem('rotisserieCart', JSON.stringify(cart));
}

function removeItem(id) {

  const itemIndex = cart.findIndex(i => i.id === id);

  if (itemIndex !== -1) {
    cart.splice(itemIndex, 1);
  }
  updateCart();
}


cartBtn.onclick = () => (cartModal.style.display = "flex");
closeCart.onclick = () => (cartModal.style.display = "none");


document.getElementById("cliente-tipo").addEventListener("change", (e) => {
  document.getElementById("endereco-box").style.display =
    e.target.value === "entrega" ? "flex" : "none";
});

// FINALIZAR PEDIDO
finalizeBtn.onclick = async () => {
  const nome = document.getElementById("cliente-nome").value.trim();
  const telefone = document.getElementById("cliente-telefone").value.trim();
  const tipo = document.getElementById("cliente-tipo").value;
  const rua = document.getElementById("cliente-rua").value.trim();
  const numero = document.getElementById("cliente-numero").value.trim();
  const bairro = document.getElementById("cliente-bairro").value.trim();
  const observacao = document.getElementById("cliente-observacao").value.trim();

  if (!nome || !telefone || !tipo) {
    alert("Preencha os dados obrigatórios!");
    return;
  }

  if (cart.length === 0) {
    alert("Seu carrinho está vazio!");
    return;
  }

  const res = await fetch("salvar_pedido.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      cart,
      nome,
      telefone,
      tipo,
      rua,
      numero,
      bairro,
      observacao
    })
  });

  const dados = await res.json();

  if (dados.sucesso) {
    alert("Pedido enviado com sucesso! Nº " + dados.id_pedido);

    // Limpa o carrinho
    cart = [];
    updateCart();

    cartModal.style.display = "none";
  } else {
    alert("Erro ao salvar pedido: " + dados.msg);
  }
};

// Sincroniza a UI na inicialização
updateCart();

// Botão de leitura dentro do modal
document.getElementById("btn-read-modal").onclick = () => {
    speechSynthesis.cancel();

    const nome = document.getElementById("modal-name").textContent;
    const desc = document.getElementById("modal-description").textContent;
    const preco = document.getElementById("modal-price").textContent;

    let texto = `${nome}. ${desc}. Preço ${preco}`;
    let fala = new SpeechSynthesisUtterance(texto);
    fala.lang = "pt-BR";

    speechSynthesis.speak(fala);
};

// =============================
// PESQUISA EM TEMPO REAL
// =============================
const searchInput = document.getElementById("searchInput");

if (searchInput) {
    searchInput.addEventListener("input", () => {
        const termo = searchInput.value.toLowerCase();

        document.querySelectorAll(".menu-card").forEach(card => {
            const nome = card.querySelector("h3").innerText.toLowerCase();
            const preco = card.querySelector(".card-price").innerText.toLowerCase();
            const desc = card.querySelector("p")?.innerText.toLowerCase() || "";

            if (nome.includes(termo) || preco.includes(termo) || desc.includes(termo)) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    });
}

function openModal(id) {
    openProductModal(id);
}
