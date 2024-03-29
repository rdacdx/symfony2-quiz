<?php

namespace Acme\ReqBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Acme\ReqBundle\Model\Quiz\QuizQuestionsGetter;
use Acme\ReqBundle\Model\Quiz\QuizQuestionsGetterWrapper;
use Acme\ReqBundle\Model\Quiz\QuizAnswersGetterWrapper;
use Acme\ReqBundle\Model\Quiz\QuizAnswersGetter;
use Acme\ReqBundle\Model\Quiz\QuizTagsGetter;
use Acme\ReqBundle\Model\Quiz\QuizTagsGetterWrapper;
use Acme\ReqBundle\Model\Quiz\QuizSetupQuestionGetterWrapper;
use Acme\ReqBundle\Model\EntitySerializer;
use Acme\ReqBundle\Model\Quiz\QuizPaginationRecordHelper;

/**
 * @author Andrea Fiori
 * @since  22 October 2014
 */
class QuizController extends Controller
{
    const perPage = 5;
    
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $topic    = $request->get('topic');
        $tag      = $request->get('tag');
        
        $quizSetupQuestionGetterWrapper= new QuizSetupQuestionGetterWrapper($em);
        $quizSetupQuestionGetterWrapper->setTopic($topic);
        $quizSetupQuestionGetterWrapper->setTag($tag);
        $quizSetupQuestionGetterWrapper->setupObjectWrapper();
        $quizSetupQuestionGetterWrapper->setupObjectWrapperInput();
        $quizSetupQuestionGetterWrapper->setupObjectWrapperQueryBuilder();

        $pagination = $this->get('knp_paginator')->paginate(
            $quizSetupQuestionGetterWrapper->getObjectWrapper()->getObjectGetter()->getQuery(),
            $this->get('request')->query->get('page', 1),
            self::perPage
        );
 
        $entitySerializer = new EntitySerializer($em);
  
        $recordsArray = array();
        foreach($pagination as $paging) {
            $quizPaginationRecordHelper = new QuizPaginationRecordHelper($paging);
            $quizPaginationRecordHelper->setupQuestion($entitySerializer->toArray($paging->getQuestion()));
            $quizPaginationRecordHelper->setupAnswers(new QuizAnswersGetterWrapper(new QuizAnswersGetter($em)));
            $quizPaginationRecordHelper->setupTopics(new QuizQuestionsGetterWrapper(new QuizQuestionsGetter($em)));
            $quizPaginationRecordHelper->formatTopicsRecords();
            $quizPaginationRecordHelper->setupTags(new QuizTagsGetterWrapper(new QuizTagsGetter($em)));
            $quizPaginationRecordHelper->formatTagsRecords();
            
            $recordsArray[] = $quizPaginationRecordHelper->getRecord();
        }

        return $this->render('AcmeReqBundle:Default:quiz_questions.html.twig', array(
            'pagination' => $pagination,
            'qa'         => $recordsArray
        ));
    }
}