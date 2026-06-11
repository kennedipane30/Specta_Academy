package models

import (
	"time"
)

type Material struct {
	MaterialID   uint      `gorm:"primaryKey;column:material_id" json:"material_id"`
	ClassID      int       `gorm:"column:class_id" json:"class_id"`
	UserID       int       `gorm:"column:user_id" json:"user_id"`        // ✅ Ubah dari sql.NullInt64 ke int
	SubjectName  string    `gorm:"column:material_name" json:"subject_name"`
	Title        string    `gorm:"column:title" json:"title"`
	Week         int       `gorm:"column:week;default:1" json:"week"`
	FilePath     string    `gorm:"column:file_path" json:"file_path"`
	CreatedAt    time.Time `json:"created_at"`
	UpdatedAt    time.Time `json:"updated_at"`
}