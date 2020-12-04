const cerrarWebview = () => {
    if ($("#conid").length > 0) {
        let conid = $("#conid").attr("data-value");
        let xhr = new XMLHttpRequest();
        let url = "https://labs357.com.ar/closeWebChatModalTv.php";
        xhr.open("POST", url, true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function() {
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

(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];

    if (d.getElementById(id)) {
        return;
    }

    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.com/en_US/messenger.Extensions.js";
    fjs.parentNode.insertBefore(js, fjs);

    const totalHeight = window.innerHeight;
    document.getElementsByClassName("container")[0].style.height = totalHeight + "px";    
}(document, 'script', 'Messenger'));

window.extAsyncInit = function() {
    try {

    } catch (e) {
        console.log(e);
    }
};

$("#file").on("change", function() {
    let s = $(this).val().split("\\");
    $("label[for='file']").text(s[s.length - 1]);
});


$(".form-control:not(.button)").click(function() {
    const formTop = $("#formData").offset().top;
    const formHeight = $("#formData").height();
    const controlTop = $(this).offset().top;
    const controlHeight = $(this).height();

    const formTotal = formTop + formHeight;
    const controlTotal = controlTop + controlHeight;

    if (controlTotal > formTotal || controlTop < formTop) {
        const xValue = controlTotal > formTotal ? controlTop : formTop - controlTop - 15;
        $("#formData").stop().animate(
            {scrollTop: xValue},
            500,
            'swing'
        );        
    }
});

$("#btnCancel").click(function() {
    eval(closeWebView);
});

$("#btnSend").on("click", function(event) {
    event.preventDefault();

    if ($("#formData")[0].reportValidity() == false) {
        return false;
    }

    $(".loader").fadeIn("slow", function() {
        $(this).css("display", "block");
    });

    let data = new FormData($("#formData")[0]);
    data.append("userid", userId);
    data.append("function", "updateformulario");

    $.ajax({
        url: "controller/formulario.controller.php",
        type: "POST",
        data: data,
        contentType: false,
        cache: false,
        processData: false,
        success: function(res) {
            console.log(res)
            $(".loader").fadeOut("slow", function() {
                $(this).css("display", "none");
            });

            cerrarWebview();
            return;
        },
        error: function(e) {
            console.log("Error Updating");
            console.log(e);
        }
    });
});