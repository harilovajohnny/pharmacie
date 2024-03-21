document.getElementById('file').addEventListener('change', function() {
    previewFile();
});

document.getElementById('successMessage').addEventListener('change', function() {
    succesmessage();
});

function previewFile() {
    var input = document.getElementById('file');
    var preview = document.getElementById('previewImage');

    if (input.files && input.files[0]) {
        var fileName = input.files[0].name;

        // Afficher le nom du fichier dans un élément HTML, par exemple, un paragraphe avec l'id "file-name"
        var fileNameElement = document.getElementById('file-name');
        if (fileNameElement) {
            fileNameElement.innerText = 'File Name: ' + fileName;

            fileNameElement.style.fontSize = 'medium';
            fileNameElement.style.color = '#15c';
            fileNameElement.style.backgroundColor = '#f5f5f5';
            fileNameElement.style.padding = '6px';
        }
    }
}

function succesmessage(){
    var successMessage = document.getElementById('successMessage');
    if (successMessage) {
        setTimeout(function() {
            successMessage.style.display = 'none';
        }, 5000); // 5000 millisecondes (5 secondes)
    }
}
// document.addEventListener('DOMContentLoaded', function() {
    
   
// });

function selectCard(card) {
// Supprime la classe "active" de toutes les cartes
    var allCards = document.querySelectorAll('.card-body');
    allCards.forEach(function (c) {
        c.classList.remove('activation');   
    });

    // Ajoute la classe "active" à la carte sélectionnée
    card.classList.add('activation');
}


// function previewImage() {
//     var input = document.getElementById('file');
//     var preview = document.getElementById('previewImage');

//     if (input.files && input.files[0]) {
//         var reader = new FileReader();

//         reader.onload = function (e) {
//             var fileType = input.files[0].type;
//             if (fileType.startsWith('image/')) {
//                 // Si c'est une image, afficher l'aperçu de l'image
//                 preview.src = e.target.result;
//             } else if (fileType === 'application/pdf') {
//                 // Si c'est un PDF, afficher un aperçu générique
//                 preview.src = 'path_vers_une_icone_pdf'; // Remplacez path_vers_une_icone_pdf par le chemin vers une icône PDF
//             }
//         };

//         reader.readAsDataURL(input.files[0]);
//     }
// }



// document.getElementById('file').addEventListener('change', function() {
//     convertImageToBase64();
// });

// function convertImageToBase64() {
//     var input = document.getElementById('file');
//     var base64ImageField = document.getElementById('base64Image');

//     if (input.files && input.files[0]) {
//         var reader = new FileReader();

//         reader.onload = function (e) {
//             var base64Image = e.target.result;
//             // Mettez à jour la valeur du champ caché avec la valeur base64Image
//             base64ImageField.value = base64Image;

//             // Mettez également à jour l'aperçu de l'image
//             var preview = document.getElementById('uploadedImage');
//             preview.src = base64Image;
//         };

//         reader.readAsDataURL(input.files[0]);
//     }
// }
