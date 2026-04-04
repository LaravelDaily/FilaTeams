<?php

declare(strict_types=1);

return [
    'pages' => [
        'edit_team'   => ['label' => 'Configuración del equipo'],
        'create_team' => ['label' => 'Crear equipo'],
    ],

    'fields' => [
        'team_name'     => ['label' => 'Nombre del equipo'],
        'name'          => ['label' => 'Nombre'],
        'email'         => ['label' => 'Correo electrónico'],
        'email_address' => ['label' => 'Dirección de correo electrónico'],
        'role'          => ['label' => 'Rol'],
        'invited_by'    => ['label' => 'Invitado por'],
        'expires'       => ['label' => 'Expira'],
    ],

    'sections' => [
        'delete_team' => ['heading' => 'Eliminar equipo'],
    ],

    'tables' => [
        'members' => [
            'heading' => 'Miembros del equipo',
        ],
        'invitations' => [
            'heading'     => 'Invitaciones pendientes',
            'empty_state' => [
                'heading'     => 'No hay invitaciones pendientes',
                'description' => 'Invite a miembros del equipo haciendo clic en el botón de arriba.',
            ],
        ],
    ],

    'actions' => [
        'delete_team' => [
            'label'              => 'Eliminar equipo',
            'modal_heading'      => 'Eliminar equipo',
            'modal_description'  => '¿Está seguro que desea eliminar este equipo? Esta acción no se puede deshacer.',
            'modal_submit_label' => 'Eliminar equipo',
        ],
        'change_role'       => ['label' => 'Cambiar rol'],
        'remove_member'     => ['label' => 'Eliminar'],
        'leave_team'        => ['label' => 'Abandonar equipo'],
        'invite_member'     => ['label' => 'Invitar miembro'],
        'cancel_invitation' => ['label' => 'Cancelar'],
    ],

    'notifications' => [
        'cannot_delete_personal_team' => ['title' => 'No se puede eliminar el equipo personal.'],
        'role_updated'                => ['title' => 'Rol actualizado.'],
        'member_removed'              => ['title' => 'Miembro eliminado.'],
        'left_team'                   => ['title' => 'Usted ha abandonado el equipo.'],
        'invitation_sent'             => ['title' => 'Invitación enviada a :email.'],
        'invitation_cancelled'        => ['title' => 'Invitación cancelada.'],
    ],

    'validation' => [
        'team_name' => [
            'reserved'       => 'Este nombre de equipo está reservado y no puede usarse.',
            'route_conflict' => 'Este nombre de equipo entra en conflicto con una ruta existente y no puede usarse.',
        ],
        'invitation' => [
            'already_member' => 'Este usuario ya es miembro del equipo.',
            'pending_exists' => 'Ya existe una invitación pendiente para este correo electrónico.',
        ],
    ],

    'mail' => [
        'invitation' => [
            'subject'       => 'Usted ha sido invitado a unirse a :team',
            'line_invited'  => ':inviter le ha invitado a unirse al equipo :team.',
            'action_accept' => 'Aceptar invitación',
            'line_expiry'   => 'Esta invitación vencerá el :date.',
        ],
    ],

    'flash' => [
        'invitation_expired'     => 'Esta invitación ha expirado.',
        'invitation_wrong_email' => 'Esta invitación fue enviada a una dirección de correo electrónico diferente.',
        'no_team'                => 'Usted debe ser miembro de un equipo para acceder a este recurso.',
        'not_member_of_any_team' => 'Usted no es miembro de ningún equipo.',
    ],

    'personal_team_name' => 'Equipo de :name',

    'roles' => [
        'owner'  => 'Propietario',
        'admin'  => 'Administrador',
        'member' => 'Miembro',
    ],
];
