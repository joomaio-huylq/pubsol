<?php
/**
 * SPT software - Model
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic model
 * 
 */

namespace App\plugins\widget\models;

use SPT\Container\Client as Base;

class WidgetModel extends Base
{ 
    private $widget_types;
    public function getTypes()
    {
        if(null === $this->widget_types)
        {
            $widget_types = [];
            $this->app->plgLoad('widgettype', 'registerType', function($types) use (&$widget_types) {
                $widget_types += $types;
            });

            $this->widget_types = $widget_types;
        }
        return $this->widget_types;
    } 

    public function getWidgetByPosition($template_id, $position)
    {
        if (!$template_id || !$position)
        {
            return false;
        }

        $where = ['template_id' => $template_id, 'position' => $position];
        $widgets = $this->WidgetEntity->list(0, 0, $where);
        $widget_types = $this->getTypes();
        foreach($widgets as &$item)
        {
            $item['path'] = isset($widget_types[$item['widget_type']]) ? $widget_types[$item['widget_type']]['path'] : '';
            $settings = $item['settings'] ? json_decode($item['settings'], true) : [];
            foreach($settings as $key => $value)
            {
                $item[$key] = $value;
            }
        }

        return $widgets;
    }
    
    public function getWidgetByTemplate($id)
    {
        if (!$id)
        {
            return false;
        }

        $widget_types = $this->getTypes();
        $widgets = $this->WidgetEntity->list(0, 0, ['template_id' => $id]);
        foreach($widgets as &$item)
        {
            $item['path'] = isset($widget_types[$item['widget_type']]) ? $widget_types[$item['widget_type']]['path'] : '';
            $settings = $item['settings'] ? json_decode($item['settings'], true) : [];
            foreach($settings as $key => $value)
            {
                $item[$key] = $value;
            }
        }

        return $widgets;
    }

    public function removeByTemplate($id)
    {
        if (!$id)
        {
            $this->error = 'Invalid id';
            return false;
        }

        $try = $this->WidgetEntity->removeByTemplate($id);
        
        return $try;
    }

    public function search($search, $position)
    {
        $where = [];
        if ($search)
        {
            $where[] = "title LIKE '%$search%'";
        }

        if ($position)
        {
            $where[] = "position Not LIKE '%($position)%'";
        }

        $list = $this->WidgetEntity->list(0,0, $where);
        $list = $list ? $list : [];
        
        return $list;
    }
}
