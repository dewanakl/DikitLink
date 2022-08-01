<?php

namespace Controllers;

use Core\Auth;
use Core\Controller;
use Core\Request;
use Models\Link;
use Models\Stat;

class StatistikController extends Controller
{
    public function index()
    {
        $lastmonth = Link::join('stats', 'links.id', 'stats.link_id')
            ->where('links.user_id', Auth::user()->id)
            ->where('stats.created_at', date('Y-m-d H:i:s.u', strtotime('-1 month')), '>=')
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->select('to_char(stats.created_at, \'YYYY-MM\') AS tgl', 'count(stats.id) as hint')
            ->get();

        $get = fn (string $select) => Link::join('stats', 'links.id', 'stats.link_id')
            ->where('links.user_id', Auth::user()->id)
            ->groupBy('stats.' . $select)
            ->orderBy('hint', 'DESC')
            ->select('stats.' . $select, 'count(stats.id) as hint')
            ->get();

        $sum = Link::leftJoin('stats', 'links.id', 'stats.link_id')
            ->where('links.user_id', Auth::user()->id)
            ->groupBy('links.user_id')
            ->select('count(distinct links.id) as jumlah_link', 'count(distinct stats.id) as total_pengunjung')
            ->first();

        return $this->view('statistik', [
            'last_month' => $lastmonth,
            'user_agent' => $get('user_agent'),
            'ip_address' => $get('ip_address'),
            'jumlah_link' => $sum->jumlah_link ?? 0,
            'total_pengunjung' => $sum->total_pengunjung ?? 0
        ]);
    }

    public function click(Request $request, $id)
    {
        $link = Link::find($id, 'name');

        if (empty($link->id)) {
            return $this->view('hilang');
        }

        Stat::create([
            'link_id' => $link->id,
            'user_agent' => $request->server('HTTP_USER_AGENT'),
            'ip_address' => $request->ip()
        ]);

        header_remove();
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . trim($link->link));
        exit();
    }
}