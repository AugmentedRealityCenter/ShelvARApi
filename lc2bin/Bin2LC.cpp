#include <iostream>
#include <string>
#include <sstream>

using namespace std;

// convert a binary string to a decimal number, returns decimal value
int bin2dec(const char *bin)
{
        int  b, k, m, n;
        int  len, sum = 0;

        len = strlen(bin) - 1;
        for(k = 0; k <= len; k++)
        {
                n = (bin[k] - '0'); // char to numeric value
                if ((n > 1) || (n < 0))
                {
                        puts("\n\n ERROR! BINARY has only 1 and 0!\n");
                        return (0);
                }
                for(b = 1, m = len; m > k; m--)
                {
                        // 1 2 4 8 16 32 64 ... place-values, reversed here
                        b *= 2;
                }
                // sum it up
                sum = sum + n * b;
                //printf("%d*%d + ",n,b);  // uncomment to show the way this works
        }
        return(sum);
}

string bin2LC(string bin1){

	//Bits0-7 are the LC fields in binary
	//Since its one long string, have to substr the
	//same string multiple times
	string bits0 = bin1.substr(0, 7);
	string bits1 = bin1.substr(7, 15);
	string bits2 = bin1.substr(7+15, 13);
	string bits3 = bin1.substr(7+15+13, 10);
	string bits4 = bin1.substr(7+15+13+10, 12);
	string bits5 = bin1.substr(7+15+13+10+12, 15);
	string bits6 = bin1.substr(7+15+13+10+12+15, 12);
	string bits7 = bin1.substr(7+15+13+10+12+15+12, 15);
	
	//The first field is three letters 
	string fld1 = "";
	if(bits0[0] == '1'){
		if(atoi(bits1.substr(0, 5).c_str())!=0){//Checks for blank space
			//Changes the binary rep of a letter to a char and add it to a field
			fld1 += (char)( bin2dec(bits1.substr(0, 5).c_str())+64);
		}
		if(atoi(bits1.substr(5, 5).c_str())!=0)//Checks for blank space
			fld1 += (char)( bin2dec(bits1.substr(5, 5).c_str())+64);

		if(atoi(bits1.substr(10, 5).c_str())!=0)//Checks for blank space
			fld1 += (char)( bin2dec(bits1.substr(10, 5).c_str())+64);
	}
	//change field to to an int
	int fld2; 
	if(bits0[1] == '1'){
		fld2 = bin2dec(bits2.c_str()); 
	}
	//change field to to an int
	int fld3; 
	if(bits0[2] == '1'){
		fld3 = bin2dec(bits3.c_str()); 
	}
	//change field to to an int
	int fld4; 
	if(bits0[3] == '1'){
		fld4 = bin2dec(bits4.c_str()); 
	}
	//fld5_0 is the letter in space 1
	//fld5_1 is the 3diget num after
	//change them to apropriate values and 
	//combine
	string fld5_0 = "";
	int fld5_1;
	string fld5 = "";
	if(bits0[4] == '1'){
		if(atoi(bits5.substr(0, 5).c_str())!=0)
			fld5_0 += (char)( bin2dec(bits5.substr(0, 5).c_str())+64);
		fld5_1 = bin2dec(bits5.substr(5, 10).c_str());

		std::stringstream ss;
		ss << fld5_1;

		fld5 += fld5_0;
		fld5 += ss.str();
	}

	//change field to to an int
	int fld6; 
	if(bits0[5] == '1'){
		fld6 = bin2dec(bits6.c_str()); 
	}

	//Same as field 5
	string fld7_0 = "";
	int fld7_1;
	string fld7 = "";
	if(bits0[6] == '1'){
		if(atoi(bits7.substr(0, 5).c_str())!=0)
			fld7_0 += (char)( bin2dec(bits7.substr(0, 5).c_str())+64);
		fld7_1 = bin2dec(bits7.substr(5, 10).c_str());

		std::stringstream ss1;
		ss1 << fld7_1;

		fld7 += fld7_0;
		fld7 += ss1.str();
	}

	std::stringstream ss2;

	ss2 << fld1 <<fld2<<fld3<<fld4<<fld5<<fld6<<fld7;

	string out = ss2.str();
	return out;
}
/*
int main(){

	string ret = "";
	ret = bin2LC("111111100001000010000100011011110001101111000001101111000000011101111000001101111000000101101111001");
	cout <<ret<<"\n";
	return 0;
}*/