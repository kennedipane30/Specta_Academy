package config

import (
	"fmt"
	"os"

	"github.com/joho/godotenv"
	"gorm.io/driver/postgres"
	"gorm.io/gorm"
)

func InitDB() *gorm.DB {
	// 1. Load file .env
	err := godotenv.Load()
	if err != nil {
		fmt.Println("Peringatan: File .env tidak ditemukan, menggunakan environment system")
	}

	// 2. Ambil variabel database dari .env
	host := os.Getenv("DB_HOST")
	user := os.Getenv("DB_USER")
	password := os.Getenv("DB_PASSWORD")
	dbname := os.Getenv("DB_NAME")
	port := os.Getenv("DB_PORT")
	sslmode := os.Getenv("DB_SSLMODE")

	// 3. Susun DSN (Data Source Name) untuk Postgres
	dsn := fmt.Sprintf("host=%s user=%s password=%s dbname=%s port=%s sslmode=%s TimeZone=Asia/Jakarta",
		host, user, password, dbname, port, sslmode)

	// 4. Buka koneksi ke Database
	db, err := gorm.Open(postgres.Open(dsn), &gorm.Config{})
	if err != nil {
		fmt.Printf("Gagal koneksi ke database %s: %v\n", dbname, err)
		panic(err)
	}

	fmt.Printf("Berhasil koneksi ke database PostgreSQL: %s\n", dbname)
	return db
}