const produits = [
  {
    nom: "Casque Gaming",
    prix: 59.99,
    image: "https://picsum.photos/300?1"
  },
  {
    nom: "Clavier RGB",
    prix: 89.99,
    image: "https://picsum.photos/300?2"
  },
  {
    nom: "Souris Pro",
    prix: 39.99,
    image: "https://picsum.photos/300?3"
  },
  {
    nom: "Écran 144Hz",
    prix: 199.99,
    image: "https://picsum.photos/300?4"
  }
];

const shop = document.getElementById("shop");

produits.forEach(p => {
  const card = document.createElement("div");
  card.classList.add("card");

  card.innerHTML = `
    <img src="${p.image}" alt="${p.nom}">
    <div class="card-content">
      <h3>${p.nom}</h3>
      <div class="price">${p.prix} €</div>
      <button onclick="acheter('${p.nom}')">Acheter</button>
    </div>
  `;

  shop.appendChild(card);
});

function acheter(nom) {
  alert("Tu as acheté : " + nom);
}

function callAPIcreerBDD() {
  fetch("http://localhost:93/creationBDD.php")
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      document.getElementById("result").textContent = "Requête envoyée: " + JSON.stringify(data);
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById("result").textContent = "Error: " + error.message;
    });
}


function callAPIListeVoiture() {
  fetch("http://localhost:93/listeVoitures.php")
    .then(res => res.json())
    .then(data => {
      let resultHTML = '';
      data.forEach(voiture => {
        resultHTML += `Immatriculation : ${voiture.immatriculation}<br>Couleur : ${voiture.couleur}<br>Kilomètres : ${voiture.km}<br><br>`;
      });
      document.getElementById("result2").innerHTML = resultHTML;
    })
    .catch(error => {
      console.error('Error fetching data:', error);
      document.getElementById("result2").innerHTML = 'Error fetching data';
    });
}

function callAPIAjoutVoitureGarage() {
  fetch("http://localhost:93/ajoutVoitureGarage.php", {
    method: "POST",
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      "immatriculation": document.getElementById("form-immat").value,
      "couleur": document.getElementById("form-couleur").value,
      "km": document.getElementById("form-km").value
    })
  })
  .then(response => response.json())
  .then(data => {
    console.log('Success:', data);
    document.getElementById("result3").textContent = "Requête envoyée: " + JSON.stringify(data);
  })
  .catch(error => {
    console.error('Error:', error);
    document.getElementById("result3").textContent = "Error: " + error.message;
  });
}
