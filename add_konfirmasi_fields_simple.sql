-- Versi alternatif tanpa IF NOT EXISTS (untuk MySQL versi lama)
-- Jalankan satu per satu, abaikan error jika kolom sudah ada

ALTER TABLE `tagihan_bulanan` ADD COLUMN `bukti_transfer` VARCHAR(255) NULL;

ALTER TABLE `tagihan_bulanan` ADD COLUMN `konfirmasi_at` TIMESTAMP NULL;

ALTER TABLE `tagihan_bulanan` ADD COLUMN `status_konfirmasi` ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none';

ALTER TABLE `tagihan_bulanan` ADD COLUMN `catatan_admin` TEXT NULL;
