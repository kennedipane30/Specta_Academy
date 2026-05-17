package models

import "time"

type Material struct {
	MaterialID   uint      `gorm:"primaryKey;column:material_id" json:"material_id"`
	ClassID      uint      `gorm:"column:class_id" json:"class_id"`
	UserID       uint      `gorm:"column:user_id" json:"user_id"`
	Title        string    `gorm:"column:title" json:"title"`
	MaterialName string    `gorm:"column:material_name" json:"material_name"`
	Week         int       `gorm:"column:week" json:"week"`
	FilePath     string    `gorm:"column:file_path" json:"file_path"`
	CreatedAt    time.Time `json:"created_at"`
	UpdatedAt    time.Time `json:"updated_at"`
}