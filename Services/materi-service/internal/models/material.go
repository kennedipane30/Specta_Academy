package models

import (
	"database/sql"
	"time"
)

type Material struct {
	MaterialID   uint           `gorm:"primaryKey;column:material_id" json:"material_id"`
	ClassID      int            `gorm:"column:class_id" json:"class_id"`
	UserID       sql.NullInt64  `gorm:"column:user_id" json:"user_id"` // Agar bisa NULL
	SubjectName  string         `gorm:"column:material_name" json:"subject_name"` // Map ke material_name di DB
	Title        string         `gorm:"column:title" json:"title"`
	Week         int            `gorm:"column:week;default:1" json:"week"`
	FilePath     string         `gorm:"column:file_path" json:"file_path"`
	CreatedAt    time.Time      `json:"created_at"`
	UpdatedAt    time.Time      `json:"updated_at"`
}