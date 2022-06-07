<?php
namespace DOMProcessor;
use DiDom\Document;
use DiDom\Query;
use DiDom\Element;
use function logger\writeLog as writeLog;

/**
 * Overwrite default params
 *
 * @param $params
 * @return array
 */
function setParams($params){
    $defaults = array();
    return array_replace_recursive($defaults, $params);
}

function addImgDeferTags(&$document){
    $deferAttrs = array(
        'decoding' => 'async',
        'loading' => 'lazy'
    );

    $imgs = $document->find('img');
    if (count($imgs) > 0){
        foreach ($imgs as $img){
            foreach ($deferAttrs as $attr => $attrValue){
                $img->setAttribute($attr, $attrValue);
            }
        }
    }
}

function startProcessing($html, $options = array()){
    // 1) получить документ из html
    // 2) обойти все img, добавив параметры

    // defaults
    $options = setParams($options);

    $document = new Document($html);

    // Начинаем работу
    // 1) Добавим к img decoding="async" & loading="lazy"
    addImgDeferTags($document);

    // Оформляем возврат
    $moddedhtml = $document->html();
    // Воюем с кодировкой
    $moddedhtml = html_entity_decode($moddedhtml);

    return $moddedhtml;
}