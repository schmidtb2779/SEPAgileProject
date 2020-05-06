-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema G7AgileExperience
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema G7AgileExperience
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `G7AgileExperience` DEFAULT CHARACTER SET utf8mb4 ;
USE `G7AgileExperience` ;

-- -----------------------------------------------------
-- Table `G7AgileExperience`.`Class`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`Class` (
  `idClass` INT(11) NOT NULL,
  `name` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idClass`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`Student`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`Student` (
  `idStudent` INT(11) NOT NULL,
  `fullName` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idStudent`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`Grader`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`Grader` (
  `idGrader` INT(11) NOT NULL,
  PRIMARY KEY (`idGrader`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`Professor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`Professor` (
  `professorID` INT(11) NOT NULL,
  `profName` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`professorID`),
  CONSTRAINT `isA`
    FOREIGN KEY (`professorID`)
    REFERENCES `G7AgileExperience`.`Grader` (`idGrader`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`Section`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`Section` (
  `sectionID` INT(11) NOT NULL,
  `classID` INT(11) NOT NULL,
  `skillsListID` INT(11) NOT NULL,
  `professorID` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`sectionID`, `classID`),
  INDEX `belongsToClass` (`classID` ASC) VISIBLE,
  INDEX `teaches` (`professorID` ASC) VISIBLE,
  CONSTRAINT `belongsToClass`
    FOREIGN KEY (`classID`)
    REFERENCES `G7AgileExperience`.`Class` (`idClass`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `teaches`
    FOREIGN KEY (`professorID`)
    REFERENCES `G7AgileExperience`.`Professor` (`professorID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`NoteSheet`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`NoteSheet` (
  `noteSheetID` INT(11) NOT NULL,
  PRIMARY KEY (`noteSheetID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`GradeType`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`GradeType` (
  `ID` INT(11) NOT NULL,
  `TypeOfGrade` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`ID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`Lab`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`Lab` (
  `idLab` INT(11) NOT NULL,
  `labName` VARCHAR(45) NULL DEFAULT NULL,
  `GradeType` INT(11) NULL DEFAULT NULL,
  `maxScore` VARCHAR(11) NULL DEFAULT '100',
  `sectionID` INT(11) NOT NULL,
  `classID` INT(11) NOT NULL,
  `noteSheetID` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`idLab`, `sectionID`, `classID`),
  INDEX `belongsToClassSection` (`sectionID` ASC, `classID` ASC) VISIBLE,
  INDEX `containsA` (`noteSheetID` ASC) VISIBLE,
  INDEX `isOfGradeType` (`GradeType` ASC) VISIBLE,
  CONSTRAINT `belongsToClassSection`
    FOREIGN KEY (`sectionID` , `classID`)
    REFERENCES `G7AgileExperience`.`Section` (`sectionID` , `classID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `containsA`
    FOREIGN KEY (`noteSheetID`)
    REFERENCES `G7AgileExperience`.`NoteSheet` (`noteSheetID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `isOfGradeType`
    FOREIGN KEY (`GradeType`)
    REFERENCES `G7AgileExperience`.`GradeType` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`Grade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`Grade` (
  `gradeID` INT(11) NOT NULL,
  `assignedValue` VARCHAR(11) NOT NULL COMMENT 'A letter score/rubric score with max 100 as default (3 digits) or complete/incomplete',
  `maxScore` VARCHAR(11) NULL DEFAULT '100',
  `labID` INT(11) NOT NULL,
  `classID` INT(11) NOT NULL,
  `sectionID` INT(11) NOT NULL,
  `studentID` INT(11) NOT NULL,
  `typeOfGrade` INT(11) NOT NULL,
  `graderID` INT(11) NOT NULL,
  PRIMARY KEY (`gradeID`),
  INDEX `for` (`classID` ASC) VISIBLE,
  INDEX `belongsTo` (`studentID` ASC) VISIBLE,
  INDEX `gradedForLab` (`labID` ASC) VISIBLE,
  INDEX `gradedForSection` (`sectionID` ASC, `classID` ASC) VISIBLE,
  INDEX `typeOfGrade` (`typeOfGrade` ASC) VISIBLE,
  INDEX `gradedByGrader` (`graderID` ASC) VISIBLE,
  CONSTRAINT `belongsTo`
    FOREIGN KEY (`studentID`)
    REFERENCES `G7AgileExperience`.`Student` (`idStudent`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `gradedByGrader`
    FOREIGN KEY (`graderID`)
    REFERENCES `G7AgileExperience`.`Grader` (`idGrader`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `gradedForLab`
    FOREIGN KEY (`labID`)
    REFERENCES `G7AgileExperience`.`Lab` (`idLab`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `gradedForSection`
    FOREIGN KEY (`sectionID` , `classID`)
    REFERENCES `G7AgileExperience`.`Lab` (`sectionID` , `classID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `typeOfGrade`
    FOREIGN KEY (`typeOfGrade`)
    REFERENCES `G7AgileExperience`.`GradeType` (`ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`CompleteIncomplete`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`CompleteIncomplete` (
  `gradeID` INT(11) NOT NULL,
  `assignedValue` VARCHAR(20) NULL DEFAULT NULL,
  PRIMARY KEY (`gradeID`),
  CONSTRAINT `isCompleteOrIncomplete`
    FOREIGN KEY (`gradeID`)
    REFERENCES `G7AgileExperience`.`Grade` (`gradeID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`EnrollsIn`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`EnrollsIn` (
  `studentID` INT(11) NOT NULL,
  `sectionID` INT(11) NOT NULL,
  `classID` INT(11) NOT NULL,
  PRIMARY KEY (`studentID`, `sectionID`, `classID`),
  INDEX `forClass` (`classID` ASC) VISIBLE,
  CONSTRAINT `enrolledIn`
    FOREIGN KEY (`studentID`)
    REFERENCES `G7AgileExperience`.`Student` (`idStudent`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `forClass`
    FOREIGN KEY (`classID`)
    REFERENCES `G7AgileExperience`.`Section` (`classID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`Rubric`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`Rubric` (
  `rubricID` INT(11) NOT NULL,
  `maxPoints` DECIMAL(5,0) NULL DEFAULT NULL,
  PRIMARY KEY (`rubricID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`GradeByRubric`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`GradeByRubric` (
  `gradeID` INT(11) NOT NULL,
  `assignedScore` DECIMAL(5,0) NULL DEFAULT NULL,
  `rubricID` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`gradeID`),
  INDEX `basedOnRubric` (`rubricID` ASC) VISIBLE,
  CONSTRAINT `basedOnRubric`
    FOREIGN KEY (`rubricID`)
    REFERENCES `G7AgileExperience`.`Rubric` (`rubricID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `gradedByRubric`
    FOREIGN KEY (`gradeID`)
    REFERENCES `G7AgileExperience`.`Grade` (`gradeID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`Log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`Log` (
  `LogID` INT(11) NOT NULL,
  `TimeStamp` DATETIME NOT NULL,
  `gradeID` INT(11) NOT NULL,
  `graderID` INT(11) NOT NULL DEFAULT '7',
  `assignedValue` VARCHAR(11) NULL,
  PRIMARY KEY (`LogID`, `graderID`),
  INDEX `storesGrade` (`gradeID` ASC) VISIBLE,
  INDEX `generatedByGraderID` (`graderID` ASC) VISIBLE,
  CONSTRAINT `generatedByGraderID`
    FOREIGN KEY (`graderID`)
    REFERENCES `G7AgileExperience`.`Grader` (`idGrader`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `storesGrade`
    FOREIGN KEY (`gradeID`)
    REFERENCES `G7AgileExperience`.`Grade` (`gradeID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`Note`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`Note` (
  `noteID` INT(11) NOT NULL,
  `noteText` VARCHAR(200) NOT NULL,
  `graderID` INT(11) NOT NULL,
  `noteSheetID` INT(11) NOT NULL,
  PRIMARY KEY (`noteID`, `noteSheetID`),
  INDEX `enters` (`graderID` ASC) VISIBLE,
  INDEX `isForNoteSheet` (`noteSheetID` ASC) VISIBLE,
  CONSTRAINT `enters`
    FOREIGN KEY (`graderID`)
    REFERENCES `G7AgileExperience`.`Grader` (`idGrader`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `isForNoteSheet`
    FOREIGN KEY (`noteSheetID`)
    REFERENCES `G7AgileExperience`.`NoteSheet` (`noteSheetID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`RangedScore`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`RangedScore` (
  `gradeID` INT(11) NOT NULL,
  `assignedScore` DECIMAL(5,0) NULL DEFAULT NULL,
  `maxScore` VARCHAR(11) NULL DEFAULT '100',
  PRIMARY KEY (`gradeID`),
  CONSTRAINT `isRangedScore`
    FOREIGN KEY (`gradeID`)
    REFERENCES `G7AgileExperience`.`Grade` (`gradeID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`RubricCriteria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`RubricCriteria` (
  `ID` INT(11) NOT NULL,
  `rubricID` INT(11) NULL DEFAULT NULL,
  `description` VARCHAR(45) NULL DEFAULT NULL,
  `maxPoints` DECIMAL(5,0) NULL DEFAULT NULL,
  PRIMARY KEY (`ID`),
  INDEX `belongsToRubric` (`rubricID` ASC) VISIBLE,
  CONSTRAINT `belongsToRubric`
    FOREIGN KEY (`rubricID`)
    REFERENCES `G7AgileExperience`.`Rubric` (`rubricID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`SI`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`SI` (
  `siID` INT(11) NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`siID`),
  CONSTRAINT `issA`
    FOREIGN KEY (`siID`)
    REFERENCES `G7AgileExperience`.`Grader` (`idGrader`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`SkillList`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`SkillList` (
  `skillListID` INT(11) NOT NULL,
  `siID` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`skillListID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`Skill`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`Skill` (
  `skillID` INT(11) NOT NULL,
  `description` VARCHAR(45) NULL DEFAULT NULL,
  `siID` INT(11) NULL DEFAULT NULL,
  `skillListID` INT(11) NULL DEFAULT NULL,
  `isLearned` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`skillID`),
  INDEX `isPartOf` (`skillListID` ASC) VISIBLE,
  INDEX `enteredBy` (`siID` ASC) VISIBLE,
  CONSTRAINT `enteredBy`
    FOREIGN KEY (`siID`)
    REFERENCES `G7AgileExperience`.`SI` (`siID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `isPartOf`
    FOREIGN KEY (`skillListID`)
    REFERENCES `G7AgileExperience`.`SkillList` (`skillListID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`ViewsNoteSheet`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`ViewsNoteSheet` (
  `graderID` INT(11) NOT NULL,
  `noteSheetID` INT(11) NOT NULL,
  PRIMARY KEY (`graderID`, `noteSheetID`),
  INDEX `ReferencesA` (`noteSheetID` ASC) VISIBLE,
  CONSTRAINT `ReferencesA`
    FOREIGN KEY (`noteSheetID`)
    REFERENCES `G7AgileExperience`.`NoteSheet` (`noteSheetID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `toBeViewedBy`
    FOREIGN KEY (`graderID`)
    REFERENCES `G7AgileExperience`.`Grader` (`idGrader`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = 'New relation generated by the Views relationship between Grader and Notesheet since M:N relationship';


-- -----------------------------------------------------
-- Table `G7AgileExperience`.`ViewsSkillList`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `G7AgileExperience`.`ViewsSkillList` (
  `siID` INT(11) NOT NULL,
  `skillsListID` INT(11) NOT NULL,
  PRIMARY KEY (`siID`, `skillsListID`),
  INDEX `References` (`skillsListID` ASC) VISIBLE,
  CONSTRAINT `IsViewedBy`
    FOREIGN KEY (`siID`)
    REFERENCES `G7AgileExperience`.`SI` (`siID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `References`
    FOREIGN KEY (`skillsListID`)
    REFERENCES `G7AgileExperience`.`SkillList` (`skillListID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
