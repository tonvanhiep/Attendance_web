<?php

namespace App\Http\Controllers\user;

use App\Events\Chat;
use App\Http\Controllers\Controller;
use App\Models\AccountsModel;
use App\Models\EmployeesModel;
use App\Models\GroupMemberModels;
use App\Models\MessageGroupModels;
use App\Models\MessageReadedModels;
use App\Models\MessagesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    //
    public function index($id = null)
    {
        $employee = new EmployeesModel();
        $group = new MessageGroupModels();
        $messageReaded = new MessageReadedModels();
        $user = $employee->getEmployees(['id' => Auth::user()->employee_id])[0];
        $messagesModel = new MessagesModel();
        $condition = [
            'id_sender' => Auth::user()->employee_id,
            'id_receiver' => $id,
        ];
        $messages = [];
        $infoGroup = null;
        if ($id != null) {
            if (!$group->checkGroupMessageExisted($condition))
                return redirect()->route('user.chat.index');
            $messages = $messagesModel->getMessages($condition);
            if (count($messages) > 0)
                $messageReaded->setMessageReaded($condition, $messages[count($messages) - 1]->id);
            else {
                $infoGroup = $messagesModel->getInfoGroup($condition);
            }
        }

        $listMessages = $messagesModel->getListMessages($condition);

        $titlePage = 'Messenger';
        return view('user.chat', compact('titlePage', 'user', 'messages', 'listMessages', 'infoGroup'));
    }

    public function private($id = null)
    {
        $employee = new EmployeesModel();
        $group = new MessageGroupModels();
        $messageReaded = new MessageReadedModels();
        $member = new GroupMemberModels();
        $messagesModel = new MessagesModel();

        $user = $employee->getEmployees(['id' => Auth::user()->employee_id])[0];
        $condition = [
            'is_private' => 1,
            'id_sender' => Auth::user()->employee_id,
            'id_receiver' => $id,
        ];
        if ($condition['is_private'] == 1) $condition['member'] = [Auth::user()->employee_id, $condition['id_receiver']];

        $group_id = $group->checkGroupMessageExisted($condition);
        if ($group_id == -1) {
            $group_id = $group->createGroupMessage($condition);
            $condition['id_receiver'] = $group_id;
            $member->addMemberIntoGroup($condition);
        } else {
            $condition['id_receiver'] = $group_id;
        }
        $messages = [];
        $infoGroup = null;
        if ($group_id != null) {
            if (!$group->checkGroupMessageExisted($condition))
                return redirect()->route('user.chat.index');
            $messages = $messagesModel->getMessages($condition);
            if (count($messages) > 0)
                $messageReaded->setMessageReaded($condition, $messages[count($messages) - 1]->id);
            else {
                $infoGroup = $messagesModel->getInfoGroup($condition);
            }
        }

        $listMessages = $messagesModel->getListMessages($condition);

        $titlePage = 'Messenger';
        return view('user.chat', compact('titlePage', 'infoGroup', 'user', 'messages', 'listMessages'));
    }

    public function storeMessage(Request $request)
    {
        $messageModel = new MessagesModel();
        $group = new MessageGroupModels();
        $member = new GroupMemberModels();
        $data = [
            'is_private' => $request->get('is_private') == 1 ? 1 : 0,
            'id_sender' => Auth::user()->employee_id,
            'id_receiver' => $request->get('id_receiver'),
            'content' => $request->get('content'),
            'reply' => $request->get('reply'),
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s', round($request->get('time') / 1000)),
        ];
        if ($data['id_receiver'] == null) return response()->json(['code' => 500]);

        if ($data['is_private'] == 1) $data['member'] = [Auth::user()->employee_id, $data['id_receiver']];
        $group_id = $group->checkGroupMessageExisted($data);

        if ($group_id == -1) {
            $group_id = $group->createGroupMessage($data);
            $data['id_receiver'] = $group_id;
            $member->addMemberIntoGroup($data);
        } else {
            $data['id_receiver'] = $group_id;
        }

        $message = $messageModel->storeMessage($data);
        $message = $messageModel->getMessages(['id_message' => $message->id, 'id_sender' => $message->id_sender, 'id_receiver' => $message->id_receiver])[0];
        $message->avatar = asset($message->avatar);
        $message->avatar_group = asset($message->avatar_group);
        // dd($message);
        if ($message->status == -1) return response()->json(['code' => 401]);

        broadcast(new Chat($message))->toOthers();

        return response()->json(['code' => 200, 'message' => $message]);
    }

    public function searchName(Request $request)
    {
        $employee = new EmployeesModel();
        $condition = [
            'search' => $request->input('name'),
            'id_sender' => Auth::user()->employee_id
        ];
        $listEmployee = $employee->searchName($condition);
        //dd($listEmployee, $condition);
        return response()->json(['employee' => $listEmployee]);
    }

    public function markReaded(Request $request)
    {
        $messageReaded = new MessageReadedModels();
        $condition = [
            'id_receiver' => $request->get('id_group'),
            'id_sender' => Auth::user()->employee_id
        ];
        $messageReaded->setMessageReaded($condition, $request->get('id_message'));
        return response()->json(['code' => 200]);
    }
}
