<?php

return array(
    'Model\Article' => array(
        'useBatchInsert' => true,
        'collection' => 'articles',
        'fields' => array(
            'title' => array(
                'type' => 'string',
                'validation' => array(
                    array('NotBlank' => NULL),
                ),
            ),
            'status' => array(
                'type' => 'string',
                'validation' => array(
                    array('NotBlank' => NULL),
                    array('Choice' => 
                        array('choices' => 
                            array('active','deleted'),
                        ),
                    ),
                ),
            ),
            'position' => array(
                'type' => 'integer',
                'validation' => array(
                    array('NotBlank' => NULL),
                ),
            ),
            'votes' => array(
                'type' => 'integer',
                'fake' => '###########'
            ),
            'points' => 'integer',
            'line'     => 'string',
            'text'     => 'string',
            'isActive' => 'boolean',
            'createdAt'     => array(
                'type' => 'date',
                'validation' => array(
                    array('NotBlank' => NULL),
                ),
            ),
            'updatedAt'     => 'date',
            'database' => array('dbName' => 'basatos', 'type' => 'string'),
        ),
        'embeddedsOne' => array(
            'source'          => array('class' => 'Model\Source'),
        ),
        'embeddedsMany' => array(
            'comments' => array('class' => 'Model\Comment'),
        ),
        'referencesOne' => array(
            'author'      => array('class' => 'Model\Author', 'field' => 'authorId', 'onDelete' => 'cascade'),
        ),
        'referencesMany' => array(
            'categories' => array('class' => 'Model\Category', 'field' => 'categoryIds', 'onDelete' => 'unset'),
        ),
        'indexes' => array(
            array(
                'keys'    => array('slug' => 1),
                'options' => array('unique' => true),
            ),
            array(
                'keys' => array('authorId' => 1, 'isActive' => 1),
            ),
        ),
    ),
    'Model\Author' => array(
        'fields' => array(
            'name' => 'string',
        ),
        'relationsManyOne' => array(
            'articles' => array('class' => 'Model\Article', 'reference' => 'author'),
        ),
    ),
    'Model\Category' => array(
        'fields' => array(
            'name' => 'string',
        ),
        'relationsManyMany' => array(
            'articles' => array('class' => 'Model\Article', 'reference' => 'categories'),
        ),
    ),
    'Model\Comment' => array(
        'isEmbedded' => true,
        'fields' => array(
            'name' => array(
                'type' => 'string',
                'fake' => 'faker::name',
                'validation' => array(
                    array('NotBlank' => NULL),
                ),
            ),
            'text' => 'string',
            'note' => 'string',
            'line' => 'string',
        ),
        'referencesOne' => array(
            'author' => array('class' => 'Model\Author', 'field' => 'authorId'),
        ),
        'referencesMany' => array(
            'categories' => array('class' => 'Model\Category', 'field' => 'categoryIds'),
        ),
        'indexes' => array(
            array(
                'keys'    => array('line' => 1),
                'options' => array('unique' => true),
            ),
            array(
                'keys' => array('authorId' => 1, 'note' => 1),
            ),
        ),
    ),
    'Model\Source' => array(
        'isEmbedded' => true,
        'fields' => array(
            'name' => array(
                'type' => 'string',
                'validation' => array(
                    array('NotBlank' => NULL),
                ),
            ),
            'text' => 'string',
            'note' => 'string',
            'line' => 'string',
            'from' => array('dbName' => 'desde', 'type' => 'string'),
        ),
        'referencesOne' => array(
            'author' => array('class' => 'Model\Author', 'field' => 'authorId'),
        ),
        'referencesMany' => array(
            'categories' => array('class' => 'Model\Category', 'field' => 'categoryIds'),
        ),
        'indexes' => array(
            array(
                'keys'    => array('name' => 1),
                'options' => array('unique' => true),
            ),
            array(
                'keys' => array('authorId' => 1, 'line' => 1),
            ),
        ),
    )
);