<?php

namespace app\exam\controller;

use think\Controller;
use think\Db;
use think\Request;

class Exam extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //调用模型查询数据并进项分页
      $data =  \app\exam\model\Exam::paginate(4);
      return view('show',['data'=>$data]);
    }

    /**
     * 显示管理员登录页面.
     *
     * @return \think\Response
     */
    public function login()
    {
        return view('login');
    }
    /**
     * 实现管理员登录
     *
     * @return \think\Response
     */
    public function loginin(Request $request)
    {
       //接值
        $param['name'] = input('name');
        $param['pwd'] = input('pwd');
        //验证
        $validate = $this->validate($param,[
            'name|用户名' => 'require',
            'pwd|密码' => 'require',
        ]);
        if($validate !==true){
            $this->error($validate);
        }
        //调用模型执行查询
       $data = Db::table('admin')->where('name',$param['name'])->find();
       if ($data){
          if ($data['pwd']==$param['pwd']){
              $this->redirect('add');
          }else{
              $this->error('密码错误');
          }
       }else{
           $this->error('用户名错误');
       }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //接值
        $param = input();
        //验证
        $validate = $this->validate($param,[
            'name|商品名称' => 'require',
            'number|销量' => 'number',
        ]);
        if($validate !==true){
            $this->error($validate);
        }
        //接收图片
        $file = request()->file('img');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                $param['img'] = '/uploads/'. $info->getSaveName();
            }
        }
        //调用模型执行添加
        $data = \app\exam\model\Exam::create($param,true);
        if ($data){
            $this->redirect('index');
        }else{
            $this->success('添加失败');
        }
    }

    /**
     * 跳转到添加页面
     *
     *
     */
    public function add()
    {
       return view('add');
    }



    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //验证参数
        if(!is_numeric($id)){
            $this->error('参数格式不正确');
        }
        //调用模型执行软删除
        $data=\app\exam\model\Exam::destroy($id);
        if ($data){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}
