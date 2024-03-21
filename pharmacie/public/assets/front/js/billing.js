$(document).ready(function () {

    function showLoader() {
        $("#loader").show();
    }
    
    // Fonction pour masquer le loader
    function hideLoader() {
        // Vous pouvez personnaliser cette fonction pour masquer votre loader
        $("#loader").hide();
    }

    // Lorsque l'utilisateur commence à saisir dans le premier champ
    $(document).ready(function () {
        // Lorsque l'utilisateur commence à saisir dans le premier champ
        $("input[name='bill_first_name']").on("input", function () {
            // Retarder la validation des champs requis de la deuxième carte jusqu'à ce qu'elle soit visible
            $("#card_doctor input[required]").prop("required", true);
        });

        $("#showSecondCard").on("click", function (e) {
            e.preventDefault();
    
            // Vérifier si les champs du premier formulaire sont remplis
            var allFieldsFilled = true;
    
            $("#card_billing input[required]").each(function () {
                if (!this.value.trim()) {
                    allFieldsFilled = false;
                    $(this).addClass("is-invalid");
                } else {
                    $(this).removeClass("is-invalid");
                }
            });
    
            if (!allFieldsFilled) {
                // Empêcher le passage à la deuxième carte si tous les champs ne sont pas remplis
                return;
            }
            
            showLoader();
            // Masquer le premier card
            $("#card_billing").hide();


            setTimeout(function() {
                hideLoader();
            }, 3000);

            // Afficher le deuxième card
            $("#card_third").show();
    
            // Mettre à jour les propriétés 'required' des inputs de la deuxième carte
            $("#card_third input[required]").prop("required", true);

            $(".steps a.step").removeClass("active");
            $(".steps a.step:nth-child(2)").addClass("active");
        });

        // Lorsque le bouton "Continue" est cliqué
        $("#showthirdCard").on("click", function (e) {

            $("input[name='delivery_name']").on("input", function () {
                // Retarder la validation des champs requis de la deuxième carte jusqu'à ce qu'elle soit visible
                $("#card_doctor input[required]").prop("required", true);
            });

            e.preventDefault();
    
            // Vérifier si les champs du premier formulaire sont remplis
            var allFieldsFilled = true;
    
            $("#card_third input[required]").each(function () {
                if (!this.value.trim()) {
                    allFieldsFilled = false;
                    $(this).addClass("is-invalid");
                } else {
                    $(this).removeClass("is-invalid");
                }
            });
    
            if (!allFieldsFilled) {
                // Empêcher le passage à la deuxième carte si tous les champs ne sont pas remplis
                return;
            }
            
            showLoader();
            // Masquer le premier card
            $("#card_third").hide();


            setTimeout(function() {
                hideLoader();
            }, 3000);

            // Afficher le deuxième card
            $("#card_doctor").show();
    
            // Mettre à jour les propriétés 'required' des inputs de la deuxième carte
            $("#card_doctor input[required]").prop("required", true);

            $(".steps a.step").removeClass("active");
            $(".steps a.step:nth-child(2)").removeClass("active");
            $(".steps a.step:nth-child(3)").addClass("active");
        });
    });


    $("#backCardbilling").on("click", function (e) {
        e.preventDefault();
        
        showLoader();
        // Masquer le deuxième card
        $("#card_third").hide();

        setTimeout(function() {
            hideLoader();
        }, 3000);
        // Afficher le premier card
        $("#card_billing").show();

        // Mettre à jour les propriétés 'required' des inputs du premier card
        // $("#card_billing input[required]").prop("required", true);

        $(".steps a.step").removeClass("active");
        $(".steps a.step:nth-child(1)").addClass("active");
    });

    // Lorsque le bouton "Retour à la première card" est cliqué
    $("#backToFirstCard").on("click", function (e) {
        e.preventDefault();
        
        showLoader();
        // Masquer le deuxième card
        $("#card_doctor").hide();


        setTimeout(function() {
            hideLoader();
        }, 3000);
        // Afficher le premier card
        $("#card_third").show();

        // Mettre à jour les propriétés 'required' des inputs du premier card
        $("#card_third input[required]").prop("required", true);

        $(".steps a.step").removeClass("active");
        $(".steps a.step:nth-child(2)").addClass("active");
    });


    // Lorsque le formulaire est soumis
    $("#checkoutBilling").on("submit", function (e) {
        e.preventDefault();

        // Vérifier si les champs du deuxième formulaire sont remplis
        var allFieldsFilled = true;

        $("#card_doctor input[required]").each(function () {
            if (!this.value.trim()) {
                allFieldsFilled = false;
                $(this).addClass("is-invalid");
            } else {
                $(this).removeClass("is-invalid");
            }
        });

        if (!allFieldsFilled) {
            // Si tous les champs ne sont pas remplis, arrêtez la soumission du formulaire
            return;
        }

        var formAction = $(this).attr("action");

        var formData = $(this).serializeArray();

        console.log('formAction',formAction);
        console.log('formData',formData);


        // Effectuez votre logique AJAX ici pour envoyer les données au serveur
        $.ajax({
            url: formAction,
            type: "POST",
            data: formData,
            success: function(response) {
                // Gérez la réponse du serveur ici
                console.log("response",response);

                if (response.redirectTo) {
                    window.location.href = response.redirectTo;
                }
            },
            error: function(error) {
                // Gérez les erreurs ici
                console.error("error",error);
            }
        });
    });
});