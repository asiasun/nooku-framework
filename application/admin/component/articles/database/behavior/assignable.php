<?php
class ArticlesDatabaseBehaviorAssignable extends KDatabaseBehaviorAbstract
{
    protected function _beforeTableUpdate(KCommandContext $context)
    {
        $data = $context->data;

        if($data->assign)
        {
            $attachment =  $this->getObject('com:attachments.model.attachments')
                                ->id($data->id)
                                ->getRow();

            $article =  $this->getObject('com:articles.model.articles')
                            ->id($attachment->relation->row)
                            ->getRow();

            if($article->image == $attachment->path)
            {
                // Toggle to remove the image
                $article->image = $article->thumbnail = null;
            }
            else
            {
                $article->image = $attachment->path;
                $article->thumbnail = $attachment->thumbnail->thumbnail;
            }

            $article->save();
        }

        return true;
    }
}