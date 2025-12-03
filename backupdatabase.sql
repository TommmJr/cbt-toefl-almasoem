/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-12.1.2-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: cbt_toefl_almasoem
-- ------------------------------------------------------
-- Server version	12.1.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `guru`
--

DROP TABLE IF EXISTS `guru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `guru` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `nip` varchar(30) NOT NULL COMMENT 'Nomor Induk Pegawai',
  `nama_lengkap` varchar(100) NOT NULL COMMENT 'Nama lengkap guru',
  `mata_pelajaran` varchar(50) NOT NULL DEFAULT 'Bahasa Inggris' COMMENT 'Mata pelajaran yang diampu',
  `no_telepon` varchar(20) DEFAULT NULL COMMENT 'Nomor telepon guru',
  `foto_profil` varchar(255) DEFAULT NULL COMMENT 'Path foto profil guru',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guru_nip_unique` (`nip`),
  KEY `guru_user_id_foreign` (`user_id`),
  KEY `guru_nip_index` (`nip`),
  FULLTEXT KEY `guru_nama_lengkap_fulltext` (`nama_lengkap`),
  CONSTRAINT `guru_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guru`
--

LOCK TABLES `guru` WRITE;
/*!40000 ALTER TABLE `guru` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `guru` VALUES
(1,2,'987654321','Test Guru','Bahasa Inggris',NULL,NULL,'2025-11-25 08:23:46','2025-11-25 08:23:46',NULL);
/*!40000 ALTER TABLE `guru` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `jawaban`
--

DROP TABLE IF EXISTS `jawaban`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jawaban` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sesi_ujian_id` bigint(20) unsigned NOT NULL,
  `soal_id` bigint(20) unsigned NOT NULL,
  `jawaban_pilihan` varchar(5) DEFAULT NULL COMMENT 'Jawaban pilihan ganda',
  `jawaban_essay` text DEFAULT NULL COMMENT 'Jawaban essay siswa',
  `jumlah_kata` int(10) unsigned DEFAULT NULL COMMENT 'Jumlah kata dalam essay',
  `is_benar` tinyint(1) DEFAULT NULL COMMENT 'TRUE jika jawaban benar (untuk pilihan ganda)',
  `skor` decimal(5,2) DEFAULT NULL COMMENT 'Skor untuk soal ini',
  `ai_score` decimal(5,2) DEFAULT NULL COMMENT 'Skor dari AI grading',
  `ai_feedback` text DEFAULT NULL COMMENT 'Feedback dari AI (JSON)',
  `is_reviewed_by_teacher` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Sudah direview guru atau belum',
  `teacher_score` decimal(5,2) DEFAULT NULL COMMENT 'Skor manual dari guru (override AI)',
  `teacher_feedback` text DEFAULT NULL COMMENT 'Feedback manual dari guru',
  `waktu_jawab` datetime DEFAULT NULL COMMENT 'Timestamp saat dijawab',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jawaban_sesi_ujian_id_soal_id_unique` (`sesi_ujian_id`,`soal_id`),
  KEY `jawaban_sesi_ujian_id_is_benar_index` (`sesi_ujian_id`,`is_benar`),
  KEY `jawaban_soal_id_is_benar_index` (`soal_id`,`is_benar`),
  CONSTRAINT `jawaban_sesi_ujian_id_foreign` FOREIGN KEY (`sesi_ujian_id`) REFERENCES `sesi_ujian` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jawaban_soal_id_foreign` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jawaban`
--

LOCK TABLES `jawaban` WRITE;
/*!40000 ALTER TABLE `jawaban` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `jawaban` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `kategori_soals`
--

DROP TABLE IF EXISTS `kategori_soals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategori_soals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori_soals`
--

LOCK TABLES `kategori_soals` WRITE;
/*!40000 ALTER TABLE `kategori_soals` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `kategori_soals` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `log_aktivitas`
--

DROP TABLE IF EXISTS `log_aktivitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_aktivitas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `aksi` varchar(100) NOT NULL COMMENT 'Jenis aksi (login, buat_ujian, submit_jawaban, dll)',
  `modul` varchar(50) NOT NULL COMMENT 'Modul sistem (auth, ujian, soal, dll)',
  `deskripsi` text DEFAULT NULL COMMENT 'Deskripsi detail aktivitas',
  `data_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Data sebelum perubahan (untuk update/delete)' CHECK (json_valid(`data_lama`)),
  `data_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Data setelah perubahan' CHECK (json_valid(`data_baru`)),
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP address user',
  `user_agent` text DEFAULT NULL COMMENT 'Browser/device info',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Waktu aktivitas',
  PRIMARY KEY (`id`),
  KEY `log_aktivitas_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `log_aktivitas_aksi_created_at_index` (`aksi`,`created_at`),
  KEY `log_aktivitas_modul_index` (`modul`),
  CONSTRAINT `log_aktivitas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_aktivitas`
--

LOCK TABLES `log_aktivitas` WRITE;
/*!40000 ALTER TABLE `log_aktivitas` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `log_aktivitas` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_users_table',1),
(2,'0001_01_01_000001_cache_table',1),
(3,'2025_11_23_182150_siswas_table',1),
(4,'2025_11_23_182156_guru_table',1),
(5,'2025_11_23_182200_ujian_table',1),
(6,'2025_11_23_182204_soal_table',1),
(7,'2025_11_23_182205_sesi_ujian_table',1),
(8,'2025_11_23_182206_jawaban_table',1),
(9,'2025_11_23_182207_nilai_table',1),
(10,'2025_11_23_184317_log_aktivitas_table',1),
(11,'2025_11_25_144241_create_token_ujians_table',1),
(12,'2025_11_25_144314_create_kategori_soals_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `nilai`
--

DROP TABLE IF EXISTS `nilai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `nilai` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sesi_ujian_id` bigint(20) unsigned NOT NULL,
  `siswa_id` bigint(20) unsigned NOT NULL,
  `ujian_id` bigint(20) unsigned NOT NULL,
  `skor_listening` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Skor listening section',
  `skor_reading` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Skor reading section',
  `skor_writing` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Skor writing section',
  `skor_total` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Total skor TOEFL',
  `jumlah_benar` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Jumlah jawaban benar',
  `jumlah_salah` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Jumlah jawaban salah',
  `jumlah_kosong` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Jumlah soal tidak dijawab',
  `is_lulus` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Lulus atau tidak berdasarkan passing score',
  `predikat` varchar(20) DEFAULT NULL COMMENT 'Predikat nilai (Excellent, Good, Fair, Poor)',
  `durasi_pengerjaan_detik` int(10) unsigned DEFAULT NULL COMMENT 'Total durasi pengerjaan dalam detik',
  `tanggal_penilaian` datetime DEFAULT NULL COMMENT 'Tanggal nilai final dihitung',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nilai_sesi_ujian_id_unique` (`sesi_ujian_id`),
  KEY `nilai_ujian_id_skor_total_index` (`ujian_id`,`skor_total`),
  KEY `nilai_siswa_id_tanggal_penilaian_index` (`siswa_id`,`tanggal_penilaian`),
  KEY `nilai_ujian_id_is_lulus_index` (`ujian_id`,`is_lulus`),
  CONSTRAINT `nilai_sesi_ujian_id_foreign` FOREIGN KEY (`sesi_ujian_id`) REFERENCES `sesi_ujian` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_ujian_id_foreign` FOREIGN KEY (`ujian_id`) REFERENCES `ujian` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nilai`
--

LOCK TABLES `nilai` WRITE;
/*!40000 ALTER TABLE `nilai` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `nilai` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sesi_ujian`
--

DROP TABLE IF EXISTS `sesi_ujian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sesi_ujian` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ujian_id` bigint(20) unsigned NOT NULL,
  `siswa_id` bigint(20) unsigned NOT NULL,
  `token_akses` varchar(100) NOT NULL COMMENT 'Token unik untuk akses ujian',
  `status` enum('belum_mulai','sedang_mengerjakan','selesai','diskualifikasi') NOT NULL DEFAULT 'belum_mulai' COMMENT 'Status pengerjaan',
  `waktu_mulai` datetime DEFAULT NULL COMMENT 'Waktu siswa mulai mengerjakan',
  `waktu_selesai` datetime DEFAULT NULL COMMENT 'Waktu siswa submit jawaban',
  `sisa_waktu_detik` int(10) unsigned DEFAULT NULL COMMENT 'Sisa waktu dalam detik (untuk resume)',
  `jumlah_tab_switch` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Hitungan ganti tab (untuk monitoring)',
  `jumlah_peringatan` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Jumlah peringatan yang diterima',
  `catatan_pengawas` text DEFAULT NULL COMMENT 'Catatan dari admin/guru pengawas',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP address siswa',
  `user_agent` text DEFAULT NULL COMMENT 'Browser/device info',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sesi_ujian_ujian_id_siswa_id_unique` (`ujian_id`,`siswa_id`),
  UNIQUE KEY `sesi_ujian_token_akses_unique` (`token_akses`),
  KEY `sesi_ujian_siswa_id_foreign` (`siswa_id`),
  KEY `sesi_ujian_ujian_id_status_index` (`ujian_id`,`status`),
  KEY `sesi_ujian_token_akses_index` (`token_akses`),
  KEY `sesi_ujian_status_waktu_mulai_index` (`status`,`waktu_mulai`),
  CONSTRAINT `sesi_ujian_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sesi_ujian_ujian_id_foreign` FOREIGN KEY (`ujian_id`) REFERENCES `ujian` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sesi_ujian`
--

LOCK TABLES `sesi_ujian` WRITE;
/*!40000 ALTER TABLE `sesi_ujian` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `sesi_ujian` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `siswa`
--

DROP TABLE IF EXISTS `siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `nis` varchar(20) NOT NULL COMMENT 'Nomor Induk Siswa',
  `nama_lengkap` varchar(100) NOT NULL COMMENT 'Nama lengkap siswa',
  `kelas` varchar(20) NOT NULL COMMENT 'Kelas siswa (contoh: XII IPA 1)',
  `jenis_kelamin` enum('L','P') NOT NULL COMMENT 'Jenis kelamin (L/P)',
  `tanggal_lahir` date DEFAULT NULL COMMENT 'Tanggal lahir siswa',
  `alamat` text DEFAULT NULL COMMENT 'Alamat lengkap siswa',
  `no_telepon` varchar(20) DEFAULT NULL COMMENT 'Nomor telepon siswa/ortu',
  `foto_profil` varchar(255) DEFAULT NULL COMMENT 'Path foto profil siswa',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswa_nis_unique` (`nis`),
  KEY `siswa_user_id_foreign` (`user_id`),
  KEY `siswa_nis_index` (`nis`),
  KEY `siswa_kelas_index` (`kelas`),
  KEY `siswa_kelas_nama_lengkap_index` (`kelas`,`nama_lengkap`),
  FULLTEXT KEY `siswa_nama_lengkap_nis_fulltext` (`nama_lengkap`,`nis`),
  CONSTRAINT `siswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswa`
--

LOCK TABLES `siswa` WRITE;
/*!40000 ALTER TABLE `siswa` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `siswa` VALUES
(1,1,'12345678','Test Siswa','XII IPA 1','L',NULL,NULL,NULL,NULL,'2025-11-25 08:23:46','2025-11-25 08:23:46',NULL);
/*!40000 ALTER TABLE `siswa` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `soal`
--

DROP TABLE IF EXISTS `soal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `soal` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ujian_id` bigint(20) unsigned NOT NULL,
  `tipe_soal` enum('listening','reading','writing') NOT NULL COMMENT 'Tipe soal TOEFL',
  `nomor_urut` int(10) unsigned NOT NULL COMMENT 'Nomor urut soal dalam ujian',
  `pertanyaan` text NOT NULL COMMENT 'Isi pertanyaan/soal',
  `audio_path` varchar(255) DEFAULT NULL COMMENT 'Path file audio untuk listening',
  `audio_duration` int(10) unsigned DEFAULT NULL COMMENT 'Durasi audio dalam detik',
  `passage` text DEFAULT NULL COMMENT 'Teks bacaan untuk reading/writing',
  `opsi_jawaban` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array opsi jawaban (A, B, C, D)' CHECK (json_valid(`opsi_jawaban`)),
  `jawaban_benar` varchar(5) DEFAULT NULL COMMENT 'Kunci jawaban (A/B/C/D atau NULL untuk essay)',
  `rubrik_penilaian` text DEFAULT NULL COMMENT 'Rubrik penilaian essay (JSON)',
  `min_kata` int(10) unsigned DEFAULT NULL COMMENT 'Minimal jumlah kata untuk essay',
  `max_kata` int(10) unsigned DEFAULT NULL COMMENT 'Maksimal jumlah kata untuk essay',
  `bobot_nilai` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Bobot poin untuk soal ini',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `soal_ujian_id_tipe_soal_nomor_urut_index` (`ujian_id`,`tipe_soal`,`nomor_urut`),
  KEY `soal_ujian_id_nomor_urut_index` (`ujian_id`,`nomor_urut`),
  CONSTRAINT `soal_ujian_id_foreign` FOREIGN KEY (`ujian_id`) REFERENCES `ujian` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `soal`
--

LOCK TABLES `soal` WRITE;
/*!40000 ALTER TABLE `soal` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `soal` VALUES
(1,1,'reading',1,'What is the main idea of the passage?',NULL,NULL,NULL,'{\"A\":\"Option A\",\"B\":\"Option B\",\"C\":\"Option C\",\"D\":\"Option D\"}','A',NULL,NULL,NULL,1,'2025-11-25 08:23:47','2025-11-25 08:23:47',NULL);
/*!40000 ALTER TABLE `soal` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `token_ujians`
--

DROP TABLE IF EXISTS `token_ujians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `token_ujians` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `token_ujians`
--

LOCK TABLES `token_ujians` WRITE;
/*!40000 ALTER TABLE `token_ujians` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `token_ujians` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `ujian`
--

DROP TABLE IF EXISTS `ujian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ujian` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `guru_id` bigint(20) unsigned NOT NULL,
  `kode_ujian` varchar(20) NOT NULL COMMENT 'Kode unik ujian (contoh: TOEFL-UAS-2025)',
  `judul` varchar(150) NOT NULL COMMENT 'Judul ujian',
  `deskripsi` text DEFAULT NULL COMMENT 'Deskripsi/instruksi ujian',
  `tipe_ujian` enum('practice','exam') NOT NULL DEFAULT 'exam' COMMENT 'Tipe: latihan atau ujian resmi',
  `durasi_menit` int(10) unsigned NOT NULL COMMENT 'Durasi ujian dalam menit',
  `waktu_mulai` datetime NOT NULL COMMENT 'Waktu mulai ujian',
  `waktu_selesai` datetime NOT NULL COMMENT 'Waktu selesai ujian',
  `target_kelas` varchar(255) DEFAULT NULL COMMENT 'Kelas yang ditargetkan (JSON array atau comma separated)',
  `passing_score` int(10) unsigned NOT NULL DEFAULT 500 COMMENT 'Nilai minimal kelulusan',
  `is_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Status publikasi ujian',
  `tab_lock_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Aktifkan fitur tab lock',
  `show_result_immediately` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Tampilkan hasil langsung setelah selesai',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ujian_kode_ujian_unique` (`kode_ujian`),
  KEY `ujian_guru_id_foreign` (`guru_id`),
  KEY `ujian_kode_ujian_index` (`kode_ujian`),
  KEY `ujian_is_published_waktu_mulai_index` (`is_published`,`waktu_mulai`),
  KEY `ujian_tipe_ujian_is_published_index` (`tipe_ujian`,`is_published`),
  KEY `ujian_waktu_mulai_waktu_selesai_index` (`waktu_mulai`,`waktu_selesai`),
  CONSTRAINT `ujian_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ujian`
--

LOCK TABLES `ujian` WRITE;
/*!40000 ALTER TABLE `ujian` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `ujian` VALUES
(1,1,'TOEFL-TEST-001','Test TOEFL ITP',NULL,'exam',120,'2025-11-25 15:23:47','2025-11-25 17:23:47',NULL,500,1,1,0,'2025-11-25 08:23:47','2025-11-25 08:23:47',NULL);
/*!40000 ALTER TABLE `ujian` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT 'Username untuk login',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email pengguna (opsional)',
  `password` varchar(255) NOT NULL COMMENT 'Password yang sudah di-hash',
  `role` enum('admin','guru','siswa') NOT NULL DEFAULT 'siswa' COMMENT 'Peran pengguna dalam sistem',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif pengguna',
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT 'Waktu terakhir login',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`),
  KEY `users_is_active_index` (`is_active`),
  KEY `users_username_is_active_index` (`username`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `users` VALUES
(1,'test_siswa','siswa@test.com','$2y$12$6gkTxRtM2JtH2MWuh1sncuTU4da1hKYXjrNYgy69SCx.BGu8n9S0y','siswa',1,NULL,NULL,'2025-11-25 08:23:46','2025-11-25 08:23:46',NULL),
(2,'test_guru','guru@test.com','$2y$12$19ehH/UW0CHzgAkIeIEc.ub9Q2Wz7hb07IzSs.Gv31z5sOOlqZ9lS','guru',1,NULL,NULL,'2025-11-25 08:23:46','2025-11-25 08:23:46',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-11-27 23:28:24
