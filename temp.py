def below_75Per(attendance):
    print("Student with attendence less than 75%:")
    highest = 0
    for name in attendance:
        n = len(attendance[name])
        li = attendance[name]
        present = 0
        for val in li:
            if val == 1:
                present += 1
        presentPercent = (present/n) * 100
        if presentPercent < 75:
            print(name)
        if(presentPercent>highest):
            highest = presentPercent
    print("Highest percentage is", highest)
        

attendance = {
   "Aman": [1,1,0,1,1],
   "Riya": [1,0,0,1,0],
   "Karan": [1,1,1,1,1]
}

below_75Per(attendance)

# list is suitable for storing attendance instead of tuple because list is a mutable data type which can be changed during execution, so if a student comes late then attendence of the student can be changed from 0 to 1