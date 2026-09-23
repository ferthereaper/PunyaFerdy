function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

function initHapusConfirm() {
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".button-hapus, .btn-hapus");
        if (!btn) return;

        // Jika tombol sudah memiliki atribut onclick confirm bawaan HTML, biarkan browser menanganinya
        if (btn.hasAttribute("onclick")) return;

        const row = btn.closest("tr");
        const nama = row ? row.querySelector("td")?.textContent.trim() : "data ini";
        const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
        
        if (!yakin) {
            e.preventDefault();
        }
    });
}

function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");
        rows.forEach(function (row) {
            const teks = row.textContent.toLowerCase();
            row.style.display = teks.includes(keyword) ? "" : "none";
        });
    });
}

function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.style.color = "red";
    span.style.fontSize = "0.85rem";
    span.style.display = "block";
    span.style.marginTop = "0.25rem";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        const judul = form.querySelector("[name='judul'], [name='nama']");
        if (judul && judul.value.trim() === "") {
            tampilkanError(judul, "Field ini wajib diisi.");
            valid = false;
        } else if (judul) {
            hapusError(judul);
        }

        const pengarang = form.querySelector("[name='pengarang']");
        if (pengarang && pengarang.value.trim() === "") {
            tampilkanError(pengarang, "Pengarang wajib diisi.");
            valid = false;
        } else if (pengarang) {
            hapusError(pengarang);
        }

        const tahun = form.querySelector("[name='tahun']");
        if (tahun) {
            const nilai = parseInt(tahun.value, 10);
            if (isNaN(nilai) || nilai < 1900 || nilai > 2026) {
                tampilkanError(tahun, "Tahun harus di antara 1900-2026.");
                valid = false;
            } else {
                hapusError(tahun);
            }
        }

        const stok = form.querySelector("[name='stok']");
        if (stok) {
            const nilai = parseInt(stok.value, 10);
            if (isNaN(nilai) || nilai < 0) {
                tampilkanError(stok, "Stok tidak boleh negatif.");
                valid = false;
            } else {
                hapusError(stok);
            }
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}

// Fungsi AJAX untuk memuat data tabel secara dinamis
async function muatDataTabel(urlJson, selectorTbody, renderCallback) {
    const tbody = document.querySelector(selectorTbody);
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    try {
        if (loading) loading.style.display = "block";
        tbody.innerHTML = "";

        const response = await fetch(urlJson);
        const data = await response.json();

        // Simulasi delay singkat
        await new Promise(resolve => setTimeout(resolve, 300));

        renderCallback(data, tbody);
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: red;">Gagal memuat data.</td></tr>`;
    } finally {
        if (loading) loading.style.display = "none";
    }
}

// Inisialisasi seluruh fitur JavaScript saat DOM siap
document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});