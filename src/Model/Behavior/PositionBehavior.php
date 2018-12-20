<?php

namespace App\Model\Behavior;

use Cake\ORM\Behavior;

class PositionBehavior extends Behavior
{


    public function newposition(array $conditions)
    {
        $position = 1;
        $r = $this->_table->find()->where($conditions)->order(['position' => 'DESC'])->first();
        if ($r && $r->position > 0) {
            $position = $r->position + 1;
        }
        return $position;
    }


    public function deleteposition($position, $conditions)
    {
        $p = $this->_table->find()->where($conditions)->order(['position' => 'DESC'])->first();

        if ($position == $p->position) {
            return;
        } else {
            $conditionssupp = ['position >' => $position];
            $conditions = array_merge($conditions, $conditionssupp);
            $query = $this->_table->query();
            $query->update()
                ->set(['position = (position -1)'])
                ->where($conditions)
                ->execute();
        }
        return;
    }


    public function changeposition($idaDeplacer, $positionVoulue, $conditions = NULL)
    {

        //infos destination
        $destination = $this->_table->find()->where(['position' => $positionVoulue, $conditions])->first();

        //infos du déplacé
        $deplace = $this->_table->find()->where(['id' => $idaDeplacer, $conditions])->first();
        $positionInitiale = $deplace->position;


        //si j'augmente la position
        if ($positionInitiale < $positionVoulue) {
            $query = $this->_table->query();
            $query->update()
                ->set(['position = (position -1)'])
                ->where(['position <=' => $positionVoulue, 'position >' => $positionInitiale, $conditions])
                ->execute();
            $query = $this->_table->query();
            $query->update()
                ->set(['position' => $positionVoulue])
                ->where(['id' => $idaDeplacer, $conditions])
                ->execute();
        }

        if ($positionInitiale > $positionVoulue) {
            $query = $this->_table->query();
            $query->update()
                ->set(['position = (position +1)'])
                ->where(['position >=' => $positionVoulue, 'position <' => $positionInitiale, $conditions])
                ->execute();
            $query = $this->_table->query();
            $query->update()
                ->set(['position' => $positionVoulue])
                ->where(['id' => $idaDeplacer, $conditions])
                ->execute();
        }

        if ($positionInitiale == $positionVoulue) {
        }
        return;

    }


}

