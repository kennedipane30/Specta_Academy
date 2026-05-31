package models

import "time"

type Material struct {
	MaterialID  uint      `gorm:"primaryKey;column:material_id" json:"material_id"`
	ClassID     int       `json:"class_id"`
	SubjectName string    `json:"subject_name"`
	Title       string    `json:"title"`
	Week        int       `json:"week"`
	FilePath    string    `json:"file_path"`
	CreatedAt   time.Time `json:"created_at"`
	UpdatedAt   time.Time `json:"updated_at"`
}