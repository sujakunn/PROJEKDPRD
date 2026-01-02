<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Sesuaikan path dengan lokasi folder assets kamu
// Diasumsikan file ini dipanggil dari folder admin/aspirasi/ (naik 2 level)
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function kirimBalasanOtomatis($emailTujuan, $namaPenerima, $isiPesanAdmin) {
    $mail = new PHPMailer(true);

    try {
        // --- Pengaturan SMTP Gmail ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'syukha3@gmail.com'; 
        
        // PENTING: Gunakan 16 digit "App Password", bukan password login Gmail biasa!
        $mail->Password   = 'abcd efgh ijkl mnop'; 
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // --- Pengirim & Penerima ---
        $mail->setFrom('syukha3@gmail.com', 'Admin Fraksi PKB');
        $mail->addAddress($emailTujuan, $namaPenerima);

        // --- Konten Email ---
        $mail->isHTML(true);
        $mail->Subject = 'Tanggapan Aspirasi - DPRD Kabupaten Tangerang';
        
        // Desain Email agar lebih profesional
        $mail->Body    = "
            <div style='font-family: sans-serif; line-height: 1.6; color: #333;'>
                <div style='background: #15803d; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h2 style='color: white; margin: 0;'>Fraksi PKB</h2>
                    <p style='color: #d1fae5; margin: 0;'>DPRD Kabupaten Tangerang</p>
                </div>
                <div style='padding: 20px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 10px 10px;'>
                    <p>Halo <strong>$namaPenerima</strong>,</p>
                    <p>Terima kasih telah menyampaikan aspirasi Anda melalui portal kami. Berikut adalah tanggapan resmi dari Admin Fraksi:</p>
                    <div style='background: #f9fafb; padding: 15px; border-left: 4px solid #15803d; font-style: italic; margin: 20px 0;'>
                        \"$isiPesanAdmin\"
                    </div>
                    <p>Aspirasi Anda sangat berharga bagi pembangunan Kabupaten Tangerang yang lebih baik.</p>
                    <br>
                    <p style='margin-bottom: 0;'>Salam hangat,</p>
                    <p><strong>Sekretariat Fraksi PKB</strong></p>
                </div>
                <div style='text-align: center; font-size: 10px; color: #9ca3af; margin-top: 20px;'>
                    Email ini dikirim secara otomatis oleh sistem layanan aspirasi.
                </div>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Debugging: uncomment line dibawah jika email tidak terkirim untuk lihat errornya
        // error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}