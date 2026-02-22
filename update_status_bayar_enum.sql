-- Update enum status_bayar untuk menambahkan CICILAN dan TUNGGAKAN
-- Jalankan di phpMyAdmin hosting

ALTER TABLE `tagihan_bulanan` 
MODIFY COLUMN `status_bayar` ENUM('BELUM_BAYAR', 'SUDAH_BAYAR', 'CICILAN', 'TUNGGAKAN') 
NOT NULL DEFAULT 'BELUM_BAYAR';
