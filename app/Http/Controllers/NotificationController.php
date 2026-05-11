<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
     // Liste toutes les notifications de l'utilisateur connecté
     public function index(Request $request)
     {
         $notifications = $request->user()->notifications()->latest()->get();

         return response()->json($notifications);
     }

     // Nombre de notifications non lues
     public function unreadCount(Request $request)
     {
         $count = $request->user()->unreadNotifications()->count();

         return response()->json(['count' => $count]);
     }

     // Marquer une notification comme lue
     public function markAsRead(Request $request, string $id)
     {
         $notification = $request->user()->notifications()->findOrFail($id);
         $notification->markAsRead();

         return response()->json(['message' => 'Notification marquée comme lue.']);
     }

     // Marquer toutes les notifications comme lues
     public function markAllAsRead(Request $request)
     {
         $request->user()->unreadNotifications()->update(['read_at' => now()]);

         return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.']);
     }

     // Supprimer une notification
     public function destroy(Request $request, string $id)
     {
         $notification = $request->user()->notifications()->findOrFail($id);
         $notification->delete();

         return response()->json(['message' => 'Notification supprimée.']);
     }

     // Supprimer toutes les notifications
     public function destroyAll(Request $request)
     {
         $request->user()->notifications()->delete();

         return response()->json(['message' => 'Toutes les notifications ont été supprimées.']);
     }
}
