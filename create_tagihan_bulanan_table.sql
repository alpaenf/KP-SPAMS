-- SQL untuk membuat tabel tagihan_bulanan
-- Jalankan SQL ini di phpMyAdmin atau HeidiSQL

USE pamsimas;

CREATE TABLE IF NOT EXISTS `tagihan_bulanan` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pelanggan_id` BIGINT UNSIGNED NOT NULL,
  `bulan` VARCHAR(7) NOT NULL COMMENT 'Format: YYYY-MM',
  `meteran_sebelum` DECIMAL(10, 2) NULL COMMENT 'Meteran bulan lalu (m³)',
  `meteran_sesudah` DECIMAL(10, 2) NULL COMMENT 'Meteran bulan ini (m³)',
  `pemakaian_kubik` DECIMAL(10, 2) NULL COMMENT 'Pemakaian dalam m³',
  `tarif_per_kubik` DECIMAL(10, 2) DEFAULT 2500.00 COMMENT 'Tarif per m³ (default Rp 2.500)',
  `ada_abunemen` TINYINT(1) DEFAULT 1 COMMENT 'Apakah dikenakan biaya abunemen',
  `biaya_abunemen` DECIMAL(10, 2) DEFAULT 3000.00 COMMENT 'Biaya abunemen (default Rp 3.000)',
  `total_tagihan` DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Total tagihan bulan ini',
  `status_bayar` ENUM('BELUM_BAYAR', 'SUDAH_BAYAR') DEFAULT 'BELUM_BAYAR',
  `keterangan` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  INDEX `idx_pelanggan_bulan` (`pelanggan_id`, `bulan`),
  UNIQUE KEY `unique_pelanggan_bulan` (`pelanggan_id`, `bulan`),
  CONSTRAINT `tagihan_bulanan_pelanggan_id_foreign` 
    FOREIGN KEY (`pelanggan_id`) 
    REFERENCES `pelanggan`(`id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Tabel tagihan_bulanan berhasil dibuat!' as message;
