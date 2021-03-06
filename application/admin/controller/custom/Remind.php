<?php

namespace app\admin\controller\custom;

use app\common\controller\Backend;
use think\Db;

/**
 * 客户提醒
 *
 * @icon fa fa-circle-o
 */
class Remind extends Backend
{
    
    /**
     * Remind模型对象
     * @var \app\admin\model\custom\Remind
     */
    protected $model = null;
    protected $dataLimit = 'personal';
    protected $dataLimitField = 'company_id';
    protected $dataCreateField = 'admin_id';
    protected $searchFields = 'custom_remind';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\custom\Remind;

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        $params = $this->request->param();
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if(input('?custom_id')) {
            $total = $this->model
                ->with(['custominfo','admin'])
                ->where($where)
					 ->where('remind.custom_id',$params['custom_id'])
					 //->where('admin_id',$this->auth->id)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['custominfo','admin'])
                ->where($where)
                ->where('remind.custom_id',$params['custom_id'])
                //->where('admin_id',$this->auth->id)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
             }else {
             	$total = $this->model
					 ->with(['custominfo','admin'])
                ->where($where)
					 ->where('admin.id',$this->auth->id)
					 
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['custominfo','admin'])
                ->where($where)
                ->where('admin.id',$this->auth->id)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
             }

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch('index');
    }
    /**
     * 添加
     */
    public function add()
    {
    	
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                //添加数据时，能够自动添加数据创建者ID
                if ($this->dataCreateField) {
                    $params[$this->dataCreateField] = $this->auth->id;
                }
                //添加数据时，能够自动添加数据所有者公司ID
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->company_id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
       
      
        	$this->assign('custom_id',$this->request->param("custom_id")); 
         return $this->view->fetch();
    }
}
