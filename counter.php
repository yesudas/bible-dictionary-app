<?php
// counter.php

// Auto-discovered: any immediate subfolder of this directory with its own
// counter.php is treated as a dictionary, so a newly added dictionary is
// consolidated here without editing this file (same approach as
// sitemap.php's dictionary discovery).
$additionalFolders = [];
foreach (scandir(__DIR__) as $entry) {
    if ($entry === '.' || $entry === '..') {
        continue;
    }
    if (is_dir(__DIR__ . '/' . $entry) && file_exists(__DIR__ . '/' . $entry . '/counter.php')) {
        $additionalFolders[] = $entry;
    }
}
sort($additionalFolders, SORT_STRING);

$consolidationIntervalSeconds = 7 * 86400; // consolidate roughly once a week

// Helper function to read/write last consolidated snapshots
function getLastConsolidatedSnapshots() {
    $snapshotFile = __DIR__ . '/counter-snapshots.json';
    if (file_exists($snapshotFile)) {
        $data = json_decode(file_get_contents($snapshotFile), true);
        return $data ?: [];
    }
    return [];
}

function saveConsolidatedSnapshots($snapshots) {
    $snapshotFile = __DIR__ . '/counter-snapshots.json';
    file_put_contents($snapshotFile, json_encode($snapshots, JSON_PRETTY_PRINT));
}

// List of common bot keywords in User-Agent
$botKeywords = [
    'bot', 'crawl', 'slurp', 'spider', 'mediapartners', 'curl', 'python', 'wget', 'baiduspider', 'bingpreview', 'facebookexternalhit', 'pingdom'
];

// Get lowercase user agent
$userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

// Check if it's a bot
$isBot = false;
foreach ($botKeywords as $keyword) {
    if (strpos($userAgent, $keyword) !== false) {
        $isBot = true;
        break;
    }
}

$visitors2 = '1';

// If not a bot, increment the counter
if (!$isBot) {
    $counterFile = __DIR__ . '/counter.txt';
    
    // If file doesn’t exist, create it with 0
    if (!file_exists($counterFile)) {
        file_put_contents($counterFile, "0");
    }

    // Open file for reading and writing
    $fp = fopen($counterFile, "c+"); // c+ = read/write, create if not exists

    if ($fp && flock($fp, LOCK_EX)) { // lock file exclusively
        // Check if consolidation should happen, based on elapsed time since the
        // last consolidation (stored separately, since counter.txt's mtime changes
        // on every visit and can't be used to infer when consolidation last ran).
        $lastSnapshots = getLastConsolidatedSnapshots();
        $lastConsolidatedAt = isset($lastSnapshots['_meta']['last_consolidated']) ? (int)$lastSnapshots['_meta']['last_consolidated'] : 0;
        $shouldConsolidate = (time() - $lastConsolidatedAt) >= $consolidationIntervalSeconds;

        // Read current count - read entire file content
        rewind($fp); // Make sure we're at the beginning
        $currentContent = fread($fp, 1024); // Read up to 1024 bytes (more than enough for a counter)
        $count = (int)trim($currentContent); // Convert to integer and trim whitespace
        
        // If file was empty or invalid, start from 0
        if ($count < 0) {
            $count = 0;
        }
        
        // Perform consolidation if needed
        if ($shouldConsolidate) {
            $deltaCount = 0;
            $newSnapshots = [];

            foreach ($additionalFolders as $folder) {
                $subCounterFile = __DIR__ . '/' . $folder . '/counter.txt';
                if (file_exists($subCounterFile)) {
                    $currentSubCount = (int)trim(file_get_contents($subCounterFile));
                    $lastSubCount = isset($lastSnapshots[$folder]) ? (int)$lastSnapshots[$folder] : 0;
                    
                    // Calculate delta (new visits since last consolidation)
                    $delta = $currentSubCount - $lastSubCount;
                    
                    // Only add positive deltas (in case of counter reset or corruption)
                    if ($delta > 0) {
                        $deltaCount += $delta;
                    }
                    
                    // Store current count as new snapshot
                    $newSnapshots[$folder] = $currentSubCount;
                } else {
                    // If file doesn't exist, preserve last snapshot
                    if (isset($lastSnapshots[$folder])) {
                        $newSnapshots[$folder] = $lastSnapshots[$folder];
                    }
                }
            }
            
            // Record when this consolidation ran, so the next check knows how long to wait
            $newSnapshots['_meta'] = ['last_consolidated' => time()];

            // Save new snapshots for next consolidation
            saveConsolidatedSnapshots($newSnapshots);
            
            // Add only the delta (new visits) to main counter
            $count += $deltaCount;
        }
    
        // Increment
        $count++;
    
        $visitors2 = $count;
    
        // Rewind and write new value
        ftruncate($fp, 0);  // clear file
        rewind($fp);
        fwrite($fp, (string)$count);
    
        fflush($fp);        // flush output
        flock($fp, LOCK_UN); // unlock
        fclose($fp);
    } else {
        // Could not open or lock file - fallback to reading existing value
        if (file_exists($counterFile)) {
            $visitors2 = (int)trim(file_get_contents($counterFile));
        } else {
            $visitors2 = 1; // Default fallback
        }
        
        if ($fp) {
            fclose($fp);
        }
    }

}

?>