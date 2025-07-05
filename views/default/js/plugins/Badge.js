function createBadge(trigger) {
	const badge = document.createElement('span');
	badge.classList.add(
		'badge',
		'absolute', 'top-1', '-right-1',
		'bg-red-700', 'dark:bg-red-500',
		'text-neutral-50', 'rounded-full',
		'font-semibold', 'text-xs', 'px-2'
	);
	trigger.appendChild(badge);
	return badge;
}

export function initBadge(trigger, count = 0) {
	trigger.dataset.popup = String(count);

	let badge = trigger.querySelector('.badge');

	if (count > 0) {
		if (!badge) badge = createBadge(trigger);
		badge.textContent = count;
	} else if (badge) {
		badge.remove();
	}
}


export const Badge = {
	init: initBadge
}