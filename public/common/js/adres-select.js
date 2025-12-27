document.addEventListener("DOMContentLoaded", function () {
    const ilSelect = document.getElementById("il");
    const ilceSelect = document.getElementById("ilce");

    const selectedIl = document.getElementById("il-selected")?.value;
    const selectedIlce = document.getElementById("ilce-selected")?.value;

    if (!ilSelect || !ilceSelect) return;

    function clearIlce() {
        ilceSelect.innerHTML = '<option value="">İlçe seçiniz</option>';
        ilceSelect.disabled = true;
    }

    // İlleri getir
    fetch("/adres/iller")
        .then((response) => response.json())
        .then((data) => {
            data.forEach(function (il) {
                const option = document.createElement("option");
                option.value = il.id;
                option.textContent = il.il;
                if (selectedIl && il.id == selectedIl) {
                    option.selected = true;
                }
                ilSelect.appendChild(option);
            });

            // Eğer önceden seçilmiş bir il varsa, ilçeleri getir
            if (selectedIl) {
                fetch(`/adres/ilceler/${selectedIl}`)
                    .then((response) => response.json())
                    .then((ilceler) => {
                        ilceSelect.innerHTML = '<option value="">İlçe seçiniz</option>';
                        ilceler.forEach(function (ilce) {
                            const option = document.createElement("option");
                            option.value = ilce.id;
                            option.textContent = ilce.ilce;
                            if (selectedIlce && ilce.id == selectedIlce) {
                                option.selected = true;
                            }
                            ilceSelect.appendChild(option);
                        });
                        ilceSelect.disabled = false;
                    });
            }
        })
        .catch((error) => {
            console.error("İller yüklenirken hata oluştu:", error);
        });

    // İl değiştiğinde ilçeleri getir
    ilSelect.addEventListener("change", function () {
        const ilId = this.value;
        clearIlce();

        if (!ilId) return;

        fetch(`/adres/ilceler/${ilId}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.length > 0) {
                    data.forEach(function (ilce) {
                        const option = document.createElement("option");
                        option.value = ilce.id;
                        option.textContent = ilce.ilce;
                        ilceSelect.appendChild(option);
                    });
                    ilceSelect.disabled = false;
                }
            })
            .catch((error) => {
                console.error("İlçeler yüklenirken hata oluştu:", error);
            });
    });
});
