//Aparece y desaparece el indicador de carga
const toggleFade = (e) => {
    if (!e) {
        return;
    }
    if (e.classList.contains("fadeOut")) {
        e.classList.add("fadeIn");
        e.classList.remove("fadeOut");
    } else if (e.classList.contains("fadeIn")) {
        e.classList.add("fadeOut");
        e.classList.remove("fadeIn");
    }
}

//Carga de la pagina.
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('descripcion_consulta').style.display = 'none';

    toggleFade(document.querySelector('.loader'));
});

// Cierra el formulario.
const cerrarWebview = () => {
    if ($("#conid").length > 0) {
        let conid = $("#conid").attr("data-value");
        let xhr = new XMLHttpRequest();
        let url = "https://labs357.com.ar/closeWebChatModalTv.php";
        xhr.open("POST", url, true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function () {
            console.log(xhr);
        };
        let data = JSON.stringify({
            "Conexion": conid
        });
        xhr.send(data);
    } else {
        const closeWebView = "MessengerExtensions.requestCloseBrowser(function success(){console.log('close');},function error(err){console.log(err);});";
        eval(closeWebView);
    }
}

(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];

    if (d.getElementById(id)) {
        return;
    }

    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.com/en_US/messenger.Extensions.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'Messenger'));

window.extAsyncInit = function () {
    try {

    } catch (e) {
        console.log(e);
    }
};

$(".form-control:not(.button)").click(function () {
    const formTop = $("#formData").offset().top;
    const formHeight = $("#formData").height();
    const controlTop = $(this).offset().top;
    const controlHeight = $(this).height();

    const formTotal = formTop + formHeight;
    const controlTotal = controlTop + controlHeight;

    if (controlTotal > formTotal || controlTop < formTop) {
        const xValue = controlTotal > formTotal ? controlTop : formTop - controlTop - 15;
        $("#formData").stop().animate(
            { scrollTop: xValue },
            500,
            'swing'
        );
    }
});

// Hace una petición al server al hacer cick en enviar.
$("#btnEnviar").on("click", function (event) {
    event.preventDefault();

    if ($("#formData")[0].reportValidity() == false) {
        return false;
    }
    const valueDescripcionConsulta = $("#descripcion_consulta").val();

    if (valueDescripcionConsulta !== "") {
        toggleFade(document.querySelector('.loader'));

        let tipo_con = document.getElementById('tipo_consulta');
        let idTipoConsulta = tipo_con.options[tipo_con.selectedIndex].value;
        let formulario = document.querySelector("#formData");
        let data = new FormData(formulario);

        data.append("conid", conid);
        data.append("idTipoConsulta", idTipoConsulta);
        data.append("function", "updateFormulario");

        $.ajax({
            url: "controller/formulario.controller.php",
            type: "POST",
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (res) {
                toggleFade(document.querySelector('.loader'));
                cerrarWebview();
                return;
            },
            error: function (e) {
                console.log("Error Updating");
                console.log(e);
            }
        });
    } else {
        const sweetAlert = swal.fire({
            icon: "warning",
            text: "La descripción no debe estar vacío, por favor, escriba su consulta",
        });
    }
});

$("#tipo_consulta").on("change", function () {
    $("#descripcion_consulta").fadeIn("slow", function () {
        $(this).css("display", "block");
    });

    $('#descripcion_consulta').val('');
});