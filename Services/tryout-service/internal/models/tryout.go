package models

import "time"

type Tryout struct {
	TryoutID  uint      `gorm:"primaryKey;column:tryout_id" json:"tryout_id"`
	ClassID   uint      `gorm:"column:class_id" json:"class_id"`
	Title     string    `gorm:"column:title" json:"title"`
	Duration  int       `gorm:"column:duration" json:"duration"`
	IsActive  bool      `gorm:"column:is_active;default:true" json:"is_active"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

type Question struct {
	QuestionID    uint      `gorm:"primaryKey;column:question_id" json:"question_id"`
	TryoutID      uint      `gorm:"column:tryout_id" json:"tryout_id"`
	ClassID       uint      `gorm:"column:class_id" json:"class_id"`
	Question      string    `gorm:"column:question" json:"question"`
	QuestionImage string    `gorm:"column:question_image" json:"question_image"`
	OptionA       string    `gorm:"column:option_a" json:"option_a"`
	OptionAImage  string    `gorm:"column:option_a_image" json:"option_a_image"`
	OptionB       string    `gorm:"column:option_b" json:"option_b"`
	OptionBImage  string    `gorm:"column:option_b_image" json:"option_b_image"`
	OptionC       string    `gorm:"column:option_c" json:"option_c"`
	OptionCImage  string    `gorm:"column:option_c_image" json:"option_c_image"`
	OptionD       string    `gorm:"column:option_d" json:"option_d"`
	OptionDImage  string    `gorm:"column:option_d_image" json:"option_d_image"`
	CorrectAnswer string    `gorm:"column:correct_answer" json:"correct_answer"`
	Explanation   string    `gorm:"column:explanation" json:"explanation"`
	CreatedAt     time.Time `json:"created_at"`
	UpdatedAt     time.Time `json:"updated_at"`
}

type TryoutResult struct {
	TryoutResultID uint      `gorm:"primaryKey;column:tryout_result_id" json:"tryout_result_id"`
	UserID         uint      `gorm:"column:user_id" json:"user_id"`
	TryoutID       uint      `gorm:"column:tryout_id" json:"tryout_id"`
	Score          int       `gorm:"column:score" json:"score"`
	TotalCorrect   int       `gorm:"column:total_correct" json:"total_correct"`
	CreatedAt      time.Time `json:"created_at"`
	UpdatedAt      time.Time `json:"updated_at"`
}

type TryoutSubmission struct {
	ID            uint      `gorm:"primaryKey;column:id" json:"id"`
	UserID        uint      `gorm:"column:user_id" json:"user_id"`
	ClassID       uint      `gorm:"column:class_id" json:"class_id"`
	SubjectName   string    `gorm:"column:subject_name" json:"subject_name"`
	Question      string    `gorm:"column:question" json:"question"`
	QuestionImage string    `gorm:"column:question_image" json:"question_image"`
	OptionA       string    `gorm:"column:option_a" json:"option_a"`
	OptionAImage  string    `gorm:"column:option_a_image" json:"option_a_image"`
	OptionB       string    `gorm:"column:option_b" json:"option_b"`
	OptionBImage  string    `gorm:"column:option_b_image" json:"option_b_image"`
	OptionC       string    `gorm:"column:option_c" json:"option_c"`
	OptionCImage  string    `gorm:"column:option_c_image" json:"option_c_image"`
	OptionD       string    `gorm:"column:option_d" json:"option_d"`
	OptionDImage  string    `gorm:"column:option_d_image" json:"option_d_image"`
	CorrectAnswer string    `gorm:"column:correct_answer" json:"correct_answer"`
	Explanation   string    `gorm:"column:explanation" json:"explanation"`
	CreatedAt     time.Time `json:"created_at"`
	UpdatedAt     time.Time `json:"updated_at"`
}