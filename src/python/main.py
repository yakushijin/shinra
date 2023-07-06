import concurrent.futures
import s_get_userToWord
import s_get_wordToUser
import s_get_allNewWord

def s_get_userToWordRun():
    s_get_userToWord.Run()

def s_get_wordToUserRun():
    s_get_wordToUser.Run()

def s_get_allNewWordRun():
    s_get_allNewWord.Run()

if __name__ == "__main__":
    executor = concurrent.futures.ProcessPoolExecutor(max_workers=10)
    
    host = "127.0.0.1"
    
    executor.submit(s_get_userToWordRun)
    executor.submit(s_get_wordToUserRun)
    executor.submit(s_get_allNewWordRun)