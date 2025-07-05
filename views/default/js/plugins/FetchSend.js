const { url: apiUrl } = AppData;

function setObjects(object = {}) {
	const form = new FormData();
	for (const [key, value] of Object.entries(object)) {
		form.append(key, value);
	}
	return form;
}

export async function FetchSend(page = '', object = {}, type = 'json') {
	const request = await fetch(`${apiUrl}${page}`, {
		method: 'POST',
		body: setObjects(object)
	});
	const response = await request[type]();
	return response;
};