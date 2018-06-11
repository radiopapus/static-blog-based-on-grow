---
$title@: Vlozhennye-formy-symfony-1-4
author@: Viktor Zharina
$order: 117
$dates:
  published: 2013-11-05 14:04:16
---
Была задача о том, чтобы разработать сервис анкет. Сервис предусматривает создание анкеты, создание вопроса для анкеты, создание нескольких вариантов ответов для вопроса. 

Ниже пример кода, как можно сделать подобное с использованием фреймворка форм Symfony 

1.4



[php]

$questionForm = new sfForm();

$this-&gt;useFields(array('name', 'description'));



foreach ($this-&gt;getObject()-&gt;getSfSurveyQuestions() as $q)

{

	$sfSurveyQuestionForm = new sfSurveyQuestionForm($q);

      $answerForm = new sfForm();



      foreach ($q-&gt;getSfSurveyAnswers() as $answer)

      {

      	$sfSurveyAnswerForm = new sfSurveyAnswerForm($answer);

            $answerForm-&gt;embedForm($answer-&gt;getId(), $sfSurveyAnswerForm);

      }



      $sfSurveyQuestionForm-&gt;embedForm('answers', $answerForm);

     $questionForm-&gt;embedForm($q-&gt;getId(), $sfSurveyQuestionForm);

}

$this-&gt;embedForm('questions', $questionForm);

[/php]