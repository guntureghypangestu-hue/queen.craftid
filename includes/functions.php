<?php
/**
 * Membuat slug yang URL-friendly dari sebuah string.
 * Contoh: "Buket Mawar Merah" menjadi "buket-mawar-merah"
 */
function create_slug($string) {
    // Mengganti karakter non-alfanumerik dengan tanda hubung
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
    // Mengubah tanda hubung ganda menjadi tunggal dan menghapus huruf besar
    return strtolower(trim($slug, '-'));
}

/**
 * Membersihkan input pengguna untuk mencegah XSS.
 * Selalu gunakan ini saat menampilkan data yang berasal dari user.
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>