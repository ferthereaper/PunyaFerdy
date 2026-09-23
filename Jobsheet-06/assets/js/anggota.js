// // Mengambil & menampilkan Daftar Anggota secara asinkron dari data/anggota.json
// async function muatDaftarAnggota() {
//     const tbody = document.querySelector(".table-responsive table tbody");
//     const loading = document.getElementById("loading-indicator");
//     if (!tbody) return;

//     loading.style.display = "block";
//     tbody.innerHTML = "";

//     try {
//         await new Promise((resolve) => setTimeout(resolve, 600));

//         const res = await fetch("../data/anggota.json");
//         if (!res.ok) {
//             throw new Error("Gagal mengambil data (status " + res.status + ")");
//         }
//         const daftarAnggota = await res.json();

//         daftarAnggota.forEach(function (anggota) {
//             const tr = document.createElement("tr");
//             tr.innerHTML =
//                 "<td>" + anggota.no_anggota + "</td>" +
//                 "<td>" + anggota.nama + "</td>" +
//                 "<td>" + anggota.alamat + "</td>" +
//                 "<td>" + anggota.no_hp + "</td>" +
//                 "<td>" +
//                 "<button type=\"button\">Edit</button> " +
//                 "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
//                 "</td>";
//             tbody.appendChild(tr);
//         });
//     } catch (err) {
//         tbody.innerHTML =
//             "<tr><td colspan=\"5\">Gagal memuat data: " + err.message + "</td></tr>";
//     } finally {
//         loading.style.display = "none";
//     }
// }

// document.addEventListener("DOMContentLoaded", muatDaftarAnggota);

// JS 6 latihan 2
document.addEventListener("DOMContentLoaded", function() {
    muatDataTabel("../data/anggota.json", "#tabel-anggota tbody", function(data, tbody) {
        data.forEach(function(item) {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${item.no_anggota}</td>
                <td>${item.nama}</td>
                <td>${item.alamat}</td>
                <td>${item.no_hp}</td>
                <td>${item.tgl_bergabung}</td>
                <td>
                    <button class="button-edit">Edit</button>
                    <button class="button-hapus">Hapus</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    });
});