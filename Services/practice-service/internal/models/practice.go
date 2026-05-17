package models

import "time"

type PracticeQuestion struct {
	PracticeQuestionID uint      `gorm:"primaryKey;column:practice_question_id" json:"practice_question_id"`
	ClassID            uint      `gorm:"column:class_id" json:"class_id"`
	Subject            string    `gorm:"column:subject" json:"subject"` // ✨ WAJIB KECIL
	Week               int       `gorm:"column:week" json:"week"`       // ✨ WAJIB KECIL
	Question           string    `gorm:"column:question" json:"question"`
	OptionA            string    `gorm:"column:option_a" json:"option_a"`
	OptionB            string    `gorm:"column:option_b" json:"option_b"`
	OptionC            string    `gorm:"column:option_c" json:"option_c"`
	OptionD            string    `gorm:"column:option_d" json:"option_d"`
	CorrectAnswer      string    `gorm:"column:correct_answer" json:"correct_answer"`
	Explanation        string    `gorm:"column:explanation" json:"explanation"`
	CreatedAt          time.Time `json:"created_at"`
	UpdatedAt          time.Time `json:"updated_at"`
}