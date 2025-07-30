ALTER TABLE theme CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)';
ALTER TABLE cursus CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)';
ALTER TABLE lessons CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)';
ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL;
ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)';
