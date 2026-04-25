<?php
require_once __DIR__ . '/../core/CitiServeData.php';
require_once __DIR__ . '/profile_image.php';

function resident_nav_h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function build_resident_navbar_context(int $userId): array
{
    $data = new CitiServeData();
    $dbUser = $data->findUserById($userId);
    $fullName = $dbUser ? (string)$dbUser->full_name : 'Resident';
    $firstName = trim(explode(' ', $fullName)[0]);
    if ($firstName === '') {
        $firstName = 'Resident';
    }

    $raw = $data->getNotificationsByUser($userId);
    $notifications = array_slice($raw, 0, 10);
    $sections = ['new' => [], 'today' => [], 'earlier' => []];
    $nowTs = time();
    foreach ($notifications as $n) {
        $body = strtolower(((string)$n['title']) . ' ' . ((string)$n['message']));
        $category = 'announcement';
        if (strpos($body, 'complaint') !== false) {
            $category = 'complaint';
        } elseif (strpos($body, 'document') !== false || strpos($body, 'request') !== false) {
            $category = 'document';
        }

        $createdTs = isset($n['created_at']) ? strtotime((string)$n['created_at']) : false;
        if ($createdTs === false) {
            $createdTs = $nowTs;
        }
        $age = max(0, $nowTs - $createdTs);
        $timeLabel = $age < 3600
            ? max(1, (int)floor($age / 60)) . 'm'
            : ($age < 86400 ? (int)floor($age / 3600) . 'h' : (int)floor($age / 86400) . 'd');
        $section = $age < 3600 ? 'new' : ($age < 86400 ? 'today' : 'earlier');

        $sections[$section][] = [
            'id' => (int)$n['id'],
            'category' => $category,
            'message' => (string)$n['message'],
            'time_label' => $timeLabel,
            'read' => ((int)$n['is_read'] === 1),
            'link' => '/CitiServe/public/notifications.php?open=' . (int)$n['id'],
            'main_icon' => '/CitiServe/frontend/dashboard/images/citiserve_notif.png',
            'badge_icon' => $category === 'complaint'
                ? '/CitiServe/frontend/dashboard/images/complaint_notif.png'
                : '/CitiServe/frontend/dashboard/images/document_notif.png',
        ];
    }

    $unreadCount = 0;
    foreach ($notifications as $n) {
        if ((int)$n['is_read'] === 0) {
            $unreadCount++;
        }
    }

    return [
        'full_name' => $fullName,
        'first_name' => $firstName,
        'avatar' => getUserProfileImagePublicPath($userId),
        'has_notif' => $unreadCount > 0,
        'notifications' => $notifications,
        'notif_sections' => $sections,
    ];
}

function render_resident_navbar(array $ctx, string $active = 'document'): void
{
    $isDashboard = $active === 'dashboard';
    $isDoc = $active === 'document';
    $isComplaint = $active === 'complaint';
    ?>
    <nav class="navbar">
      <a href="/CitiServe/public/dashboard.php" class="navbar-logo">
        <img src="/CitiServe/frontend/dashboard/images/logo_pink.png" alt="CitiServe">
      </a>
      <div class="navbar-nav">
        <a href="/CitiServe/public/dashboard.php" class="nav-item<?= $isDashboard ? ' active' : '' ?>"><span class="nav-text">Dashboard</span></a>
        <div class="nav-item has-dropdown<?= $isDoc ? ' active' : '' ?>" id="navDocReq">
          <span class="nav-text">Document Requests</span><span class="nav-chevron">⏷</span>
          <div class="nav-dropdown">
            <a href="/CitiServe/public/request_select.php" class="nav-dropdown-item">Request Document</a>
            <a href="/CitiServe/public/my_requests.php" class="nav-dropdown-item">My Requests</a>
          </div>
        </div>
        <div class="nav-item has-dropdown<?= $isComplaint ? ' active' : '' ?>" id="navComplaint">
          <span class="nav-text">Complaint Management</span><span class="nav-chevron">⏷</span>
          <div class="nav-dropdown">
            <a href="/CitiServe/public/complaint_create.php" class="nav-dropdown-item">Submit Complaint</a>
            <a href="/CitiServe/public/my_complaints.php" class="nav-dropdown-item">My Complaints</a>
          </div>
        </div>
      </div>
      <div class="navbar-right">
        <button class="notif-btn" id="notifBtn"
          data-has-notif="<?= $ctx['has_notif'] ? '1' : '0' ?>"
          data-img-on="/CitiServe/frontend/dashboard/images/with_notif.png"
          data-img-off="/CitiServe/frontend/dashboard/images/no_notif.png"
          data-img-active="/CitiServe/frontend/dashboard/images/select_notif.png"
          title="Notifications">
          <img id="notifIcon"
            src="<?= $ctx['has_notif'] ? '/CitiServe/frontend/dashboard/images/with_notif.png' : '/CitiServe/frontend/dashboard/images/no_notif.png' ?>"
            alt="Notifications">
        </button>
        <div class="notif-panel" id="notifPanel" aria-label="Notifications">
          <div class="notif-panel-header"><span class="notif-panel-title">Notifications</span><button class="notif-panel-more" title="More options">···</button></div>
          <div class="notif-tabs"><button class="notif-tab active" data-filter="all">All</button><button class="notif-tab" data-filter="document">Document</button><button class="notif-tab" data-filter="complaint">Complaint</button></div>
          <div class="notif-list" id="notifList">
            <?php foreach (['new' => 'New', 'today' => 'Today', 'earlier' => 'Earlier'] as $key => $label): ?>
              <?php if (!empty($ctx['notif_sections'][$key])): ?>
                <div class="notif-section-label" data-section="<?= resident_nav_h($key) ?>"><?= resident_nav_h($label) ?></div>
                <?php foreach ($ctx['notif_sections'][$key] as $n): ?>
                  <div class="notif-item <?= $n['read'] ? '' : 'unread' ?>" data-id="<?= (int)$n['id'] ?>" data-category="<?= resident_nav_h($n['category']) ?>" data-link="<?= resident_nav_h($n['link']) ?>">
                    <div class="notif-icon-wrap">
                      <img class="notif-icon-main" src="<?= resident_nav_h($n['main_icon']) ?>" alt="">
                      <?php if (!empty($n['badge_icon'])): ?><img class="notif-icon-badge" src="<?= resident_nav_h($n['badge_icon']) ?>" alt=""><?php endif; ?>
                    </div>
                    <div class="notif-text"><div class="notif-msg"><?= resident_nav_h((string)$n['message']) ?></div><div class="notif-time"><?= resident_nav_h($n['time_label']) ?></div></div>
                    <div class="notif-dot"></div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            <?php endforeach; ?>
            <?php if (empty($ctx['notifications'])): ?><div class="notif-empty">No notifications yet.</div><?php endif; ?>
          </div>
          <div class="notif-footer"><button class="notif-see-prev" id="notifSeePrev"><p>See previous notifications</p></button></div>
        </div>
        <div class="profile-pill" id="profilePill">
          <div class="profile-avatar"><img src="<?= resident_nav_h((string)($ctx['avatar'] ?? '/CitiServe/frontend/dashboard/images/profile_icon.png')) ?>" alt="Profile"></div>
          <span class="profile-name"><?= resident_nav_h((string)$ctx['first_name']) ?></span>
          <span class="profile-chevron"><img src="/CitiServe/frontend/dashboard/images/profile_dropdown.png" alt=""></span>
        </div>
        <div class="profile-panel" id="profilePanel">
          <div class="profile-panel-top"><div class="profile-panel-fullname"><?= resident_nav_h((string)$ctx['full_name']) ?></div><div class="profile-panel-subtext">Resident • Brgy. Kalayaan</div></div>
          <a href="/CitiServe/public/profile.php" class="profile-panel-item"><img src="/CitiServe/frontend/dashboard/images/my_profile.png" alt="My Profile" class="profile-panel-icon1"><span>My Profile</span></a>
          <a href="/CitiServe/public/logout.php" class="profile-panel-item logout"><img src="/CitiServe/frontend/dashboard/images/logout.png" alt="Logout" class="profile-panel-icon2"><span>Logout</span></a>
        </div>
      </div>
    </nav>
    <?php
}

