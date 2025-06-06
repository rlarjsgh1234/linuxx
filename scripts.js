const adminPasswordInput = document.getElementById('admin-password');
const newQuestionInput = document.getElementById('new-question');
const createPollBtn = document.getElementById('create-poll-btn');
const adminMsg = document.getElementById('admin-msg');

const pollList = document.getElementById('poll-list');
const voteOptions = document.getElementById('vote-options');
const resultDiv = document.getElementById('result');

// 서버에서 투표 목록 불러오기
async function fetchPolls() {
    try {
        const res = await fetch('get_polls.php');
        const polls = await res.json();
        pollList.innerHTML = '<option value="">투표 주제 선택</option>';
        polls.forEach(poll => {
            const option = document.createElement('option');
            option.value = poll.id;
            option.textContent = poll.question;
            pollList.appendChild(option);
        });
    } catch (e) {
        console.error('투표 목록 불러오기 실패', e);
    }
}

// 투표 생성 버튼 클릭 시
createPollBtn.addEventListener('click', async () => {
    const password = adminPasswordInput.value.trim();
    const question = newQuestionInput.value.trim();

    if (!password || !question) {
        adminMsg.textContent = '비밀번호와 투표 주제를 모두 입력하세요.';
        return;
    }

    try {
        const res = await fetch('create_poll.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({password, question})
        });
        const data = await res.json();
        if (data.success) {
            adminMsg.textContent = '새 투표가 생성되었습니다!';
            newQuestionInput.value = '';
            adminPasswordInput.value = '';
            fetchPolls();
        } else {
            adminMsg.textContent = '오류: ' + data.message;
        }
    } catch (e) {
        adminMsg.textContent = '서버 통신 실패';
        console.error(e);
    }
});

// 투표 주제 선택 시
pollList.addEventListener('change', () => {
    if (pollList.value) {
        voteOptions.style.display = 'block';
        fetchResults(pollList.value);
    } else {
        voteOptions.style.display = 'none';
        resultDiv.innerHTML = '';
    }
});

// 투표 버튼 클릭 시
voteOptions.addEventListener('click', async (e) => {
    if (e.target.classList.contains('vote-btn')) {
        const voteOption = e.target.dataset.vote;
        const pollId = pollList.value;

        if (!pollId) {
            alert('투표 주제를 선택하세요.');
            return;
        }

        try {
            const res = await fetch('submit_vote.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({pollId, voteOption})
            });
            const data = await res.json();
            if (data.success) {
                fetchResults(pollId);
            } else {
                alert('투표 실패: ' + data.message);
            }
        } catch (e) {
            alert('서버 통신 실패');
            console.error(e);
        }
    }
});

const deletePollBtn = document.getElementById('delete-poll-btn');

// 삭제 버튼 클릭 시
deletePollBtn.addEventListener('click', async () => {
    const pollId = pollList.value;
    const password = prompt('관리자 비밀번호를 입력하세요');

    if (!pollId) {
        alert('삭제할 투표를 선택하세요.');
        return;
    }
    if (!password) {
        alert('비밀번호가 필요합니다.');
        return;
    }

    if (!confirm('정말 이 투표를 삭제하시겠습니까?')) return;

    try {
        const res = await fetch('delete_poll.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({pollId, password})
        });
        const data = await res.json();
        if (data.success) {
            alert('투표가 삭제되었습니다.');
            fetchPolls(); // 목록 새로고침
            resultDiv.innerHTML = '';
            voteOptions.style.display = 'none';
        } else {
            alert('삭제 실패: ' + data.message);
        }
    } catch (e) {
        alert('서버 통신 실패');
        console.error(e);
    }
});

// 투표 결과 불러오기
async function fetchResults(pollId) {
    try {
        const res = await fetch(`get_results.php?pollId=${pollId}`);
        const data = await res.json();
        if (data.success) {
            const yes = data.results.yes || 0;
            const no = data.results.no || 0;
            const total = yes + no;
            const yesPercent = total ? ((yes / total) * 100).toFixed(1) : 0;
            const noPercent = total ? ((no / total) * 100).toFixed(1) : 0;

            resultDiv.innerHTML = `
                <h3>투표 결과</h3>
                <p>찬성: ${yes} 표 (${yesPercent}%)</p>
                <p>반대: ${no} 표 (${noPercent}%)</p>
                <p>총 투표수: ${total} 표</p>
            `;
        } else {
            resultDiv.innerHTML = '결과를 불러올 수 없습니다.';
        }
    } catch (e) {
        resultDiv.innerHTML = '서버 통신 실패';
        console.error(e);
    }
}

// 페이지 로드 시 투표 목록 불러오기
fetchPolls();