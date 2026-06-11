package models

import (
	"time"
)

type PracticeQuestion struct {
	PracticeQuestionID uint      `gorm:"primaryKey;column:practice_question_id" json:"practice_question_id"`
	ClassID            uint      `gorm:"column:class_id;index:idx_class_subject_week" json:"class_id"`
	Subject            string    `gorm:"column:subject;size:100;index:idx_class_subject_week" json:"subject"`
	Week               int       `gorm:"column:week;index:idx_class_subject_week" json:"week"`
	Question           string    `gorm:"column:question;type:text" json:"question"`
	OptionA            string    `gorm:"column:option_a" json:"option_a"`
	OptionB            string    `gorm:"column:option_b" json:"option_b"`
	OptionC            string    `gorm:"column:option_c" json:"option_c"`
	OptionD            string    `gorm:"column:option_d" json:"option_d"`
	CorrectAnswer      string    `gorm:"column:correct_answer;size:1" json:"correct_answer"`
	Hint               string    `gorm:"column:hint;type:text" json:"hint"`
	Explanation        string    `gorm:"column:explanation;type:text" json:"explanation"`
	CreatedAt          time.Time `json:"created_at"`
	UpdatedAt          time.Time `json:"updated_at"`
}

func (PracticeQuestion) TableName() string {
	return "practice_questions"
}