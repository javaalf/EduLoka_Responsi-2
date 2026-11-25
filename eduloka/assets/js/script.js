// EduLoka - validasi form sisi klien (Modul 4 JavaScript: onSubmit="return cek()")
// Validasi ini hanya kenyamanan pengguna; server (PHP) tetap memeriksa ulang semua input.
// Singkatan untuk document.getElementById, supaya penulisan kode lebih pendek.
function $(id) { return document.getElementById(id); }

// Tandai satu kolom sebagai SALAH: beri atribut aria-invalid (garis merah lewat CSS), isi pesan di span err-NAMA, dan kembalikan 1 (dihitung sebagai satu kesalahan).
function salah(id, pesan) {
    var el = $(id);
    if (!el) return 0;
    el.setAttribute("aria-invalid", "true");
    var s = $("err-" + id);
    if (s) s.textContent = pesan;
    return 1;
}
// Hapus tanda salah dan pesan error lama dari daftar kolom sebelum validasi dijalankan ulang.
function bersih(ids) {
    ids.forEach(function (id) {
        var el = $(id);
        if (el) el.removeAttribute("aria-invalid");
        var s = $("err-" + id);
        if (s) s.textContent = "";
    });
}
// Fokuskan kursor ke kolom salah pertama. Hasilnya true (boleh dikirim) hanya bila jumlah kesalahan = 0.
// Nilai true/false inilah yang dibaca oleh onsubmit=return cek...() pada form.
function selesaiCek(jumlahSalah) {
    var f = document.querySelector("[aria-invalid='true']");
    if (f) f.focus();
    return jumlahSalah === 0;
}
// Kotak konfirmasi sebelum menghapus. confirm() mengembalikan true bila pengguna menekan OK.
function konfirmasi(pesan) { return confirm(pesan); }

// Validasi form DAFTAR (dipanggil oleh onsubmit): email, username, password.
function cekDaftar() {
    var ids = ["email", "username", "password"], x = 0;
    bersih(ids);
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test($("email").value.trim())) x += salah("email", "Tulis email yang valid, contoh nama@email.com.");
    if (!/^[A-Za-z0-9_]{4,16}$/.test($("username").value.trim())) x += salah("username", "Username 4-16 karakter: huruf, angka, atau garis bawah.");
    if ($("password").value.length < 6) x += salah("password", "Password minimal 6 karakter.");
    return selesaiCek(x);
}

// Validasi form LOGIN: username dan password tidak boleh kosong.
function cekLogin() {
    var ids = ["username", "password"], x = 0;
    bersih(ids);
    if ($("username").value.trim() === "") x += salah("username", "Isi username.");
    if ($("password").value === "") x += salah("password", "Isi password.");
    return selesaiCek(x);
}

// Validasi form BOOKING: mentor dipilih, topik minimal 5 huruf, tanggal tidak boleh lewat, jam dipilih.
function cekBooking() {
    var ids = ["id_mentor", "topik", "tanggal", "jam"], x = 0;
    bersih(ids);
    if ($("id_mentor").value === "") x += salah("id_mentor", "Pilih salah satu mentor.");
    if ($("topik").value.trim().length < 5) x += salah("topik", "Tulis topik minimal 5 huruf.");
    var t = $("tanggal").value;
    if (t === "") x += salah("tanggal", "Pilih tanggal sesi.");
    else if (new Date(t + "T23:59") < new Date()) x += salah("tanggal", "Pilih tanggal hari ini atau sesudahnya.");
    if ($("jam").value === "") x += salah("jam", "Pilih jam sesi.");
    return selesaiCek(x);
}

// Validasi form FORUM. Dipakai dua form: tanya baru (ada judul, isi min. 10) dan jawab (hanya isi, min. 3).
function cekDiskusi() {
    var x = 0;
    bersih(["judul", "isi"]);
    if ($("judul") && $("judul").value.trim().length < 8) x += salah("judul", "Tulis pertanyaan minimal 8 huruf.");
    var min = $("judul") ? 10 : 3;
    if ($("isi").value.trim().length < min) x += salah("isi", "Isi minimal " + min + " huruf.");
    return selesaiCek(x);
}

// Validasi form PROFIL: nama tanpa angka atau simbol, telepon (opsional) 8-20 digit, dan foto bila dipilih.
function cekProfil() {
    var x = 0;
    bersih(["nama_lengkap", "no_telepon", "foto"]);
    if (!/^[A-Za-z .,'\-]{3,100}$/.test($("nama_lengkap").value.trim())) x += salah("nama_lengkap", "Nama 3-100 huruf, tanpa angka atau simbol.");
    var t = $("no_telepon").value.trim();
    if (t !== "" && !/^[0-9+ ]{8,20}$/.test(t)) x += salah("no_telepon", "Nomor telepon 8-20 digit.");
    x += cekFile("foto", ["image/jpeg", "image/png", "image/webp"], 2, false, "Foto harus JPG, PNG, atau WebP.");
    return selesaiCek(x);
}

// Validasi form BUKU di admin. wajibPdf = true saat tambah buku (PDF harus dipilih).
function cekBuku(wajibPdf) {
    var x = 0;
    bersih(["judul", "mata_pelajaran", "penulis", "berkas_pdf", "gambar_sampul"]);
    if ($("judul").value.trim().length < 3) x += salah("judul", "Isi judul buku, minimal 3 huruf.");
    if ($("mata_pelajaran").value.trim() === "") x += salah("mata_pelajaran", "Isi mata pelajaran.");
    if ($("penulis").value.trim() === "") x += salah("penulis", "Isi nama penulis.");
    x += cekFile("berkas_pdf", ["application/pdf"], 25, wajibPdf, "Berkas harus berformat PDF.");
    x += cekFile("gambar_sampul", ["image/jpeg", "image/png", "image/webp"], 3, false, "Sampul harus JPG, PNG, atau WebP.");
    return selesaiCek(x);
}

// Periksa file input: jenis, ukuran (MB), dan wajib/tidak
// Periksa input file: jenis (f.type), ukuran (f.size dalam byte, maksMB dikali 1024 kali 1024), dan wajib atau tidak.
function cekFile(id, tipeBoleh, maksMB, wajib, pesanTipe) {
    var el = $(id);
    if (!el || !el.files || el.files.length === 0) return wajib ? salah(id, "Berkas wajib dipilih.") : 0;
    var f = el.files[0];
    if (tipeBoleh.indexOf(f.type) === -1) return salah(id, pesanTipe);
    if (f.size > maksMB * 1024 * 1024) return salah(id, "Ukuran berkas maksimal " + maksMB + " MB.");
    return 0;
}

// Penghitung karakter (Modul 4 Percobaan 6): <textarea data-maks="500" data-hitung="sisa">
// Penghitung karakter. Dijalankan setelah halaman selesai dimuat (DOMContentLoaded).
document.addEventListener("DOMContentLoaded", function () {
    var areas = document.querySelectorAll("textarea[data-maks]");
    areas.forEach(function (a) {
        var maks = parseInt(a.getAttribute("data-maks"), 10);
        var out = $(a.getAttribute("data-hitung"));
        function hitung() {
            if (a.value.length > maks) a.value = a.value.substring(0, maks);
            if (out) out.textContent = "Sisa " + (maks - a.value.length) + " karakter";
        }
        a.addEventListener("keyup", hitung);
        a.addEventListener("input", hitung);
        hitung();
    });
});
