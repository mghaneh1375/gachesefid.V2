@extends('layouts.form')

@section('head')
    @parent
@stop

@section('main')

    <?php $level = Auth::user()->level; ?>
    
    @if($level == getValueInfo('studentLevel'))
        @include('layouts.studentProfile')
    @elseif($level == getValueInfo('namayandeLevel') || $level == getValueInfo('schoolLevel') || 
        $level == getValueInfo('superAdminLevel') || $level == getValueInfo('adminLevel'))
        @include('layouts.schoolProfile')
    @endif

@stop