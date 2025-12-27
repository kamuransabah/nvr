function ResmiSil() {
    document.querySelectorAll("[data-img-delete='true']").forEach(button => {
        button.addEventListener("click", function (event) {
            event.preventDefault(); // Butonun varsayılan davranışını engelle

            Swal.fire({
                title: "Resmi Silmek İstediğinize Emin misiniz?",
                text: "Bu işlem geri alınamaz!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Evet, Sil!",
                cancelButtonText: "İptal"
            }).then((result) => {
                if (result.isConfirmed) {
                    const imageContainer = this.closest("#image-container"); // Resmi içeren divi bul

                    if (imageContainer) {
                        imageContainer.remove(); // Resim ve butonu kaldır
                    }

                    document.getElementById("delete_resim").value = "1"; // Input'u güncelle

                    Swal.fire({
                        title: "Silindi!",
                        text: "Resim başarıyla kaldırıldı.",
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });
    });
}

// Sayfa yüklendiğinde otomatik çalıştır
document.addEventListener("DOMContentLoaded", function () {
    ResmiSil();
});

document.addEventListener("DOMContentLoaded", function() {
    document.addEventListener("click", function(event) {
        var target = event.target.closest("[data-confirm-delete]"); // Tıklanan element veya en yakın parent'ını bul

        if (target) {
            event.preventDefault(); // Varsayılan davranışı engelle

            Swal.fire({
                title: "Emin misiniz?",
                text: "Bu veriyi silmek istediğinize emin misiniz?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Evet, sil!",
                cancelButtonText: "Vazgeç"
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = target.closest("form");

                    if (form) {
                        form.submit(); // Eğer buton bir form içindeyse formu gönder
                    } else if (target.tagName === "A") {
                        // Eğer <a href=""> etiketi ise, yeni bir form oluşturarak DELETE isteği gönder
                        var deleteForm = document.createElement("form");
                        deleteForm.action = target.href;
                        deleteForm.method = "POST";
                        deleteForm.style.display = "none";
                        deleteForm.innerHTML = `
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        `;
                        document.body.appendChild(deleteForm);
                        deleteForm.submit();
                    }
                }
            });
        }
    });
});


document.addEventListener("DOMContentLoaded", function () {
    if (typeof showAlert === "function" && typeof alertData !== "undefined") {
        showAlert(alertData.library, alertData.type, alertData.message);
    }
});

function showAlert(library, type, message) {
    let titleMap = {
        success: 'İşlem Başarılı',
        error: 'Hata Oluştu',
        warning: 'Uyarı',
        info: 'Bilgi'
    };

    switch (library) {
        case "sweetalert":
            Swal.fire({
                title: titleMap[type] || 'Bildirim',
                text: message,
                icon: type,
            });
            break;

        case "toastr":
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toastr-bottom-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr[type](message);
            break;

        case "bootstrap":
            let alertContainer = document.getElementById("alert-container");
            if (alertContainer) {
                let alertClass = "alert-" + (type === "error" ? "danger" : type);
                let alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>`;
                alertContainer.innerHTML = alertHtml;
            }
            break;

        default:
            console.error("Geçersiz alert kütüphanesi: " + library);
    }
}


